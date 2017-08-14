<?php

function ticketing_engine_admin_settings_form($form, &$form_state){

}

function ticketing_engine_admin_price_listing(){
  $render  = array();
  $package_code = '';

  $render['form'] = drupal_get_form('ticketing_engine_admin_pricing_filter_form');
  if (!empty($_SESSION['pricing_filter_package_code'])) {
    $package_code = $_SESSION['pricing_filter_package_code'];
  }

  if (!empty($package_code)) {

    $headers = array(
      'Product Code',
      'Package Code',
      'Dated',
      'Number of Days',
      'Day',
      'Night',
      'Cruise',
      'Description',
      'Age Category',
      'Price',
      'Tour Description',
      'Manage Tour',
    );

    $rows = array();

    $offerings = ticketing_engine_get_offerings_for_package($package_code);

    if (!empty($offerings)) {
      foreach ($offerings as $offering) {
        $tour = $offering->getTour();

        $rows[] = array(
          'data'  => array(
            $offering->getProductCode(),
            $offering->getPackage(),
            $offering->isDated() ? 'D' : 'O',
            $offering->getNumberOfDays(),
            $offering->isBus(),
            $offering->isNight(),
            $offering->isCruise(),
            $offering->getDescription(),
            $offering->getAgeCategory(),
            $offering->getPrice(),
            $offering->getTourDescription(),
            $tour ? l('Edit', 'taxonomy/term/' . $tour->tid . '/edit', array('query' => array('destination' => 'admin/opentourny/ticketing-engine/pricing'))) : l('Add', 'admin/structure/taxonomy/tours/add', array(
              'query' => array(
                'name'        => $offering->getDescription(),
                'tour_code'   => $offering->getTourCode(),
                'destination' => 'admin/opentourny/ticketing-engine/pricing',
              )
            )),
          ),
          'style' => array($tour ? '' : 'background-color:#CE7777;'),
        );
      }
    }

    $render['table'] = array(
      '#theme'  => 'table',
      '#header' => $headers,
      '#rows'   => $rows,
    );
  }

  return $render;
}

function ticketing_engine_admin_pricing_filter_form($form, &$form_state) {
  $options = package_get_package_options_by_package_code();

  if(empty($options)){
    $form['message']['#markup'] = t('Assign package codes to packages and upload a pricing file');
  }
  else{
    $form['package_code'] = array(
      '#type'          => 'select',
      '#title'         => t('Package'),
      '#options'       => $options,
      '#required'      => TRUE,
      '#default_value' => !empty($_SESSION['pricing_filter_package_code']) ? $_SESSION['pricing_filter_package_code'] : '',
    );

    $form['submit'] = array(
      '#type'  => 'submit',
      '#value' => t('Submit'),
    );
  }

  return $form;
}

function ticketing_engine_admin_pricing_filter_form_submit($form, &$form_state) {
  unset($_GET['page']);
  $_SESSION['pricing_filter_package_code'] = $form_state['values']['package_code'];
}

function ticketing_engine_admin_price_upload_form($form, &$form_state) {
  $form['csv'] = array(
    '#type'              => 'managed_file',
    '#title'             => t('Upload a CSV file'),
    '#required'          => TRUE,
    '#upload_location'   => 's3://pricing',
    '#upload_validators' => array(
      'file_validate_extensions' => array('csv'),
    ),
  );

  $form['submit'] = array(
    '#type'  => 'submit',
    '#value' => t('Submit'),
  );

  return $form;
}

function ticketing_engine_admin_price_upload_form_validate($form, &$form_state) {
  if (form_get_errors()) {
    return;
  }

  $file      = file_load($form_state['values']['csv']);
  $csv       = preg_replace('~\R~u', "\n", file_get_contents($file->uri));
  $offerings = array();

  if ($file) {
    file_delete($file);

    $validate = _ticketing_engine_validate_csv($csv);
    if ($validate !== TRUE) {
      form_set_error('csv', $validate);
    }
  }
  else {
    form_set_error('csv', 'Error uploading file.');
  }

  if (form_get_errors()) {
    return;
  }

  foreach (str_getcsv($csv, "\n") as $num_row => $row) {
    if ($num_row == 0) {
      continue;
    }
    $cols        = str_getcsv($row);
    $offerings[] = TicketingEngineOffering::loadFromCSVRow($cols);
  }

  ticketing_engine_rebuild_tables($offerings);
}

function ticketing_engine_admin_price_upload_form_submit($form, &$form_state) {
  $form_state['redirect'] = 'admin/opentourny/ticketing-engine/pricing';
}

function ticketing_engine_admin_transaction_listing_form($form, &$form_state){
  $form['#method'] = 'get';

  $params = drupal_get_query_parameters();

  if(empty($params['op']) || $params['op'] != 'Reset'){
    $params['date_from'] = strtotime($params['date_from']['date']);
    $params['date_to'] = strtotime($params['date_to']['date'].' 11:59:59pm');
  }
  else{
    $params = array();
  }

  $form['provider_reference'] = array(
    '#type' => 'textfield',
    '#title' => t('Transaction ID'),
    '#default_value' => !empty($params['provider_reference']) ? $params['provider_reference'] : '',
  );

  $form['date_from'] = array(
    '#type' => 'date_popup',
    '#title' => t('Order Date From'),
    '#date_format' => 'm/d/Y',
    '#default_value' => !empty($params['date_from']) ? date('Y-m-d', $params['date_from']) : '',
  );

  $form['date_to'] = array(
    '#type' => 'date_popup',
    '#title' => t('Order Date To'),
    '#date_format' => 'm/d/Y',
    '#default_value' => !empty($params['date_to']) ? date('Y-m-d', $params['date_to']) : '',
  );

  $form['braintree_transaction_id'] = array(
    '#type' => 'textfield',
    '#title' => t('Braintree ID'),
    '#default_value' => !empty($params['braintree_transaction_id']) ? $params['braintree_transaction_id'] : '',
  );

  $form['tour_name'] = array(
    '#type' => 'textfield',
    '#title' => t('Tour Name'),
    '#default_value' => !empty($params['tour_name']) ? $params['tour_name'] : '',
  );

  $form['first_name'] = array(
    '#type' => 'textfield',
    '#title' => t('First Name'),
    '#default_value' => !empty($params['first_name']) ? $params['first_name'] : '',
  );

  $form['last_name'] = array(
    '#type' => 'textfield',
    '#title' => t('Last Name'),
    '#default_value' => !empty($params['last_name']) ? $params['last_name'] : '',
  );

  $form['email'] = array(
    '#type' => 'textfield',
    '#title' => t('Email'),
    '#default_value' => !empty($params['email']) ? $params['email'] : '',
  );

  $form['coupon_code'] = array(
    '#type' => 'select',
    '#title' => t('Coupon Code'),
    '#options' => coupon_get_coupon_options(),
    '#default_value' => !empty($params['coupon_code']) ? $params['coupon_code'] : '',
  );

  $form['actions'] = array(
    '#type' => 'actions',
    '#weight' => 1,
  );

  $form['actions']['submit'] = array(
    '#type' => 'submit',
    '#value' => t('Submit'),
  );

  $form['actions']['export'] = array(
    '#type' => 'submit',
    '#value' => t('Export'),
  );

  $form['actions']['reset'] = array(
    '#type' => 'submit',
    '#value' => t('Reset'),
  );

  $params['paginate'] = FALSE;
  if(empty($params['op']) || $params['op'] != 'Export'){
    $params['paginate'] = TRUE;
  }

  $transaction_listings = ticketing_engine_transactions_get_transactions_listing($params);

  if($params['op'] == 'Export'){
    data_export_csv_export($transaction_listings['rows'], $transaction_listings['headers'], 'transactions');
  }
  else{
    $form['table'] = array(
      '#markup' => theme('table', array('header' => $transaction_listings['headers'], 'rows' => $transaction_listings['rows'])).theme('pager'),
      '#weight' => 10,
    );

    return $form;
  }
}

/**
 * @param TicketingEngineTransaction $transaction
 */
function ticketing_engine_admin_resend_transaction_email_callback($transaction){
  if(module_exists('cart')){
    if($transaction){
      cart_send_transaction_email($transaction);
      drupal_set_message(t('Transaction :transaction_number email has been resent to :email', array(
        ':transaction_number' => $transaction->getProviderReference(),
        ':email' => $transaction->getEmail()
      )));
    }
    else{
      drupal_set_message(t('There was a problem loading the transaction'), 'error');
    }
  }
  else{
    drupal_set_message(t('Cart module must be enabled to send transaction e-mails'), 'error');
  }

  drupal_goto('admin/opentourny/ticketing-engine/transactions');
}

/**
 * @param string $csv
 *
 * @return bool/string
 */
function _ticketing_engine_validate_csv($csv) {
  foreach (str_getcsv($csv, "\n") as $num_row => $row) {
    if ($num_row == 0) {
      continue;
    }
    $cols = str_getcsv($row);
    if (count($cols) != 10) {
      return format_string('Row !row: Invalid number of columns', array('!row' => $num_row + 1));
    }
    foreach ($cols as $delta => $col) {
      $message = '';
      $col = trim($col);

      switch ($delta) {
        case 2:
          if (strtoupper($col) != 'O' && strtoupper($col) != 'D') {
            $message = 'Row !row: Invalid code on column !col';
          }
          break;
        case 3:
          if (!is_numeric($col) || $col <= 0) {
            $message = 'Row !row: Invalid number on column !col';
          }
          break;
        case 4:
        case 5:
        case 6:
          if (!is_numeric($col) || ($col != 0 && $col != 1)) {
            $message = 'Row !row: Invalid code on column !col';
          }
          break;
        case 8:
          if (strtoupper($col) != 'A' && strtoupper($col) != 'C') {
            $message = 'Row !row: Invalid code on column !col';
          }
          break;
        case 9:
          if (!is_numeric($col)) {
            $message = 'Row !row: Invalid price on column !col';
          }
          if ($col < 0) {
            $message = 'Row !row: Negative price on column !col';
          }
          break;
      }

      if (!empty($message)) {
        return format_string($message, array(
          '!row' => $num_row + 1,
          '!col' => $delta + 1
        ));
      }
    }
  }
  return TRUE;
}