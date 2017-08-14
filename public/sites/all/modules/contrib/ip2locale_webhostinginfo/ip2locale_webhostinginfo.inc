<?php
/**
 * @file
 * Include file for IP to Country module.
 */

function ip2locale_webhostinginfo_batch_prepare() {
  $csv_file = 'private://ip2locale/webhostinginfo/ip-to-country.csv';

  $operations = array();
  $operations[] = array('_ip2locale_webhostinginfo_file_prepare', array($csv_file));
  $operations[] = array('_ip2locale_webhostinginfo_batch_insert', array($csv_file));
  
  //$recover = variable_get('ip2locale_webhostinginfo_get_zip_done', FALSE) | variable_get('ip2locale_webhostinginfo_extract_zip_done', FALSE) | variable_get('ip2locale_webhostinginfo_store_location_csv_done', FALSE);
  // Figure this out later. Setting above variables was/is not properly implemented yet.
  $recover = FALSE;

  $batch = array(
    'title' => t('Importing IP to Locale Webhosting.info database'),
    'operations' => $operations,
    'init_message'     => $recover ? t('Recovering...') : t('Starting...'),
    'progress_message' => '',
    'error_message' => t('The import process has encountered an error.'),
    'finished' => '_ip2locale_webhostinginfo_batch_insert_finished',
    'file' => drupal_get_path('module', 'ip2locale_webhostinginfo') . '/ip2locale_webhostinginfo.inc',
  );
  batch_set($batch);
  return $batch;
}

function ip2locale_webhostinginfo_batch_insert($ajax = FALSE) {
  $batch =& batch_get();
  if (empty($batch)) {
    $batch = ip2locale_webhostinginfo_batch_prepare();
  }
  
  // If we are running in an ajax callback, don't run in progressive mode
  if ($ajax) {
    $batch =& batch_get();
    $batch['progressive'] = FALSE;
    batch_process();
  }
}
  
function _ip2locale_webhostinginfo_batch_insert($file, &$context) {

  // Work out how many lines we have.
  if (empty($context['sandbox'])) {
    
    if (!$file || !file_exists($file)) {
      drupal_set_message(t("The ip2locale_webhostinginfo database file @file is missing. ", array('@file' => $file)), 'error');
      return FALSE;
    }
    $fd = fopen($file, 'r');
    if (!$fd) {
      drupal_set_message(t("Could not open the ip2locale_webhostinginfo database (@file) for reading. Please check it's permissions.", array('@file' => $file)), 'error');
      return FALSE;
    }
    
    $context['sandbox']['progress'] = 0;
    $context['sandbox']['current_line'] = 0;
    $context['sandbox']['max'] = count(file($file));
    $context['sandbox']['file'] = $file;
    
    // flush the existing entries
    db_delete('ip2locale_webhostinginfo')->execute();
  }
  else {
    $fd = fopen($context['sandbox']['file'], 'r');
  }

  // insert some records
  $query = db_insert('ip2locale_webhostinginfo')->fields(array('ip_from', 'ip_to', 'iso2', 'iso3', 'name'));
  
  $count = 0;
  $start = $context['sandbox']['current_line'];
  $end = $context['sandbox']['current_line'] + 5000;
  
  while ($line = fgets($fd)) {
    if ($count >= $start && $count < $end) {
      if (preg_match('/^"([0-9]+)","([0-9]+)","(.{2})","(.{3})","(.*)"$/', trim($line), $matches)) {
        $query->values(array(
          'ip_from' => $matches[1],
          'ip_to' => $matches[2],
          'iso2' => $matches[3],
          'iso3' => $matches[4],
          'name' => $matches[5],
        ));
        $context['results'][] = $line;
      }
      $context['sandbox']['progress']++;
      $context['sandbox']['current_line']++;
    }
    $count++;
  }
 
  $query->execute();
  fclose($fd);

  if ($context['sandbox']['progress'] != $context['sandbox']['max']) {
    $context['finished'] = $context['sandbox']['progress'] / $context['sandbox']['max'];
  }
  else {
    drupal_unlink($file);
  }
  // Record the current status for use in ajax progress bar
  variable_set('ip2locale_webhostinginfo_update_progress_message', t('@count of @total', array('@count' => $context['sandbox']['progress'], '@total' => $context['sandbox']['max'])));
  variable_set('ip2locale_webhostinginfo_update_progress_percent', $context['finished']);
  variable_set('ip2locale_webhostinginfo_last_update', REQUEST_TIME);
}

function _ip2locale_webhostinginfo_batch_insert_finished($success, $results, $operations) {
  if ($success) {
    $message = format_plural(count($results), 'One record imported.', '@count records imported.');
  }
  else {
    $message = t('Finished with an error.');
  }
  drupal_set_message($message);
  
  // Reset the status variables.
  variable_set('ip2locale_webhostinginfo_update_progress_message', '');
  variable_set('ip2locale_webhostinginfo_update_progress_percent', 0);
}

function _ip2locale_webhostinginfo_inet_aton($a) {
  $ip = gethostbyname($a);
  return sprintf("%u", ip2long($ip));
}

/**
 * Get the total count of IP ranges in database
 */
function _ip2locale_webhostinginfo_get_count() {
  $sql   = "SELECT COUNT(*) FROM {ip2locale_webhostinginfo}";
  $count = db_query($sql)->fetchField();
  return (int) $count;
}


/**
 * Make sure a CSV file exists for processing.
 * If it does not exist yet, a ZIP file is extracted in the same dir.
 * If that ZIP file does not exist yet, it's downloaded from a fixed location.
 *
 * @param string $csv_file
 *   Filename / stream wrapper URI, where CSV is expected to be (extracted).
 * @param array $context
 *   Batch API context
 */
function _ip2locale_webhostinginfo_file_prepare($csv_file, &$context) {

  $src = variable_get('database_source', 'http://ip-to-country.webhosting.info/downloads/ip-to-country.csv.zip');

  $csv_file = file_stream_wrapper_uri_normalize($csv_file);

  if (file_exists($csv_file)) {
    $context['message']  = t('CSV file found on disk');
    $context['finished'] = TRUE;
    return;
  }

  // Ensure local directory exists.
  $path = drupal_dirname($csv_file);
  if (!file_prepare_directory($path, FILE_CREATE_DIRECTORY | FILE_MODIFY_PERMISSIONS)) {
    // Private file system path not defined then stop the process
    $message = t('Private file system path not defined. Please define the private file system path !here.',  array('!here' => l('here', 'admin/config/media/file-system')));
    $context['results']['#abort'] = $message;
    $context['message'] = $message;
    return;
  }
  
  variable_set('ip2locale_webhostinginfo_update_progress_message', t('Fetching latest database'));

  // Check presence of ZIP file in destination directory. If present, process
  // the last one.
  $existing_zip_file = end(file_scan_directory($path, '/\.zip$/'));

  if ($existing_zip_file) {
    // Manually uploaded?
    // See NOTE below.
    $zip_file = $path . '/' . $existing_zip_file->filename;
    // TODO remove any remaining ZIP files besides $zip_file?
  }
  else {

    // Get ZIP file from remote source.
    if (@copy($src, $csv_file . '.zip')) {
      $zip_file = $csv_file . '.zip';

      // NOTE: it is possible to return at this point and set
      // $context['finished'] to 0.5 so the Batch API calls this function again
      // later, but then you should account for the possibility that the next
      // call comes from a separate HTTP request. That is: this
      // batch-operation-function should be reachable from the main .module!
    }
    else {
      $message = t('Download %src file failed. Be sure %src exists.',  array('%src' => $src));
      $context['results']['#abort'] = $message;
      $context['message'] = $message;
      return;
    }
  }

  if (!class_exists('ZipArchive')) {
    $message = t('ZIP function unavailable for PHP.',  array());
    $context['results']['#abort'] = $message;
    $context['message'] = $message;
    return;
  }

  // Unzip file
  try {
    // Zip doesn't work with stream wrappers
    $zip_file = drupal_realpath($zip_file);
    $zipArchive = new ZipArchive();
    $result = $zipArchive->open($zip_file);
    if ($result !== TRUE) {
      $message = t('Opening ZIP file generated error: %e.',  array('%e' => $result));
      $context['results']['#abort'] = $message;
      $context['message'] = $message;
      return;
    }
    if (!$zipArchive->extractTo($path)) {
      $message = t('Extracting ZIP file generated error',  array());
      $context['results']['#abort'] = $message;
      $context['message'] = $message;
      return;
    }
    $zipArchive->close();
    // NOTE: we assume the unzipped filename to be ip-to-country.csv!
  }
  catch (Exception $e) {
    $message = t('Error encountered in ZIP processing: %e',  array('%e' => $e->getMessage()));
    $context['results']['#abort'] = $message;
    $context['message'] = $message;
    return;
  }

  if (!file_exists($csv_file)) {
    $message = t('Extracting ZIP file did not create file %f',  array('%f ' => $csv_file));
    $context['results']['#abort'] = $message;
    $context['message'] = $message;
    return;
  }

  drupal_unlink($zip_file);

  // Finished. Set a message depending on $existing_zip_file.
  // See NOTE above if you want to change this.
  if ($existing_zip_file) {
    $context['message']  = t('Extracted file %zip which was already present. (Manually uploaded?)', array('%zip' => $zip_file));
  }
  else {
    $context['message']  = t('Downloaded and extracted zip file.');
  }
  $context['finished'] = TRUE;
}
