<?php 

/**
 * Menu callback and form builder for admin/settings/ip2locale.
 */
function ip2locale_redirect_admin_settings($form, &$form_state) {
  $options = array(
    IP2LOCALE_REDIRECT_FIRST_REQUEST_ONLY => t('First request only'),
    IP2LOCALE_REDIRECT_ALL_REQUESTS => t('All requests'),
  );
  $form['ip2locale_redirect_status'] = array(
    '#type' => 'radios',
    '#title' => t('Redirection status'),
    '#description' => t("IP redirection status"),
    '#options' => array(0 => t('Disabled'), 1 => t('Enabled')),
    '#default_value' => variable_get('ip2locale_redirect_status', 1),
  );
  $form['ip2locale_redirect_mode'] = array(
    '#type' => 'radios',
    '#title' => t('Redirection mode'),
    '#description' => t("Select the mechanism used to determine when redirection should be applied."),
    '#options' => $options,
    '#default_value' => variable_get('ip2locale_redirect_mode', IP2LOCALE_REDIRECT_ALL_REQUESTS),
  );
   $form['ip2locale_preserve_user_language'] = array(
    '#type' => 'checkbox',
    '#title' => t('Use prefered language in user account.'),
    '#description' => t("If user is logged in, use language preferences of it's account."),
    '#default_value' => variable_get('ip2locale_preserve_user_language', FALSE),
  );
  $form['ip2locale_redirect_save_locale'] = array(
    '#type' => 'checkbox',
    '#title' => t("Save user's locale upon exit."),
    '#description' => t("This option saves the users current language in their session, ensuring that the next time they visit the site they will start on the same language as the previous time. This only works for users that have cookies enabled in their browser."),
    '#default_value' => variable_get('ip2locale_redirect_save_locale', TRUE),
  );
  $form['ip2locale_redirect_custom_mapping'] = array(
    '#type' => 'textarea',
    '#title' => t('Country Code to Language Code mappings'),
    '#description' => t('A list of custom mappings from ISO 3166 2-character <em>Country Code</em> to Drupal <em>Language Code</em>, one on each line. eg.<br/>gb,en<br/>ie,en<br/>us,en'),
    '#default_value' => variable_get('ip2locale_redirect_custom_mapping', ''),
    '#element_validate' => array('_ip2locale_redirect_validate_custom_mapping'),
  );
  $form['ip2locale_redirect_pages'] = array(
    '#type' => 'textarea',
    '#title' => t('Pages for which redirection should be performed'),
    '#default_value' => variable_get('ip2locale_redirect_pages', "*"),
    '#cols' => 40,
    '#rows' => 5,
    '#description' => t("Enter one page per line as Drupal paths. The '*' character is a wildcard. Example paths are '<em>blog</em>' for the blog page and '<em>blog/*</em>' for every personal blog. '<em>&lt;front&gt;</em>' is the front page."),
  );
  return system_settings_form($form);
}

/**
 * Validation callback. Ensure keys are not duplicated.
 */
function _ip2locale_redirect_validate_custom_mapping($element, $form_state) {
  // Check for duplicate key values to prevent unexpected data loss.
  if (!empty($element['#value'])) {
    $lines = explode("\n", $element['#value']);
    $existing_keys = array();
    $invalid_maps = array();
    $duplicate_keys = array();
    $group = '';
    foreach ($lines as $line) {
      $matches = array();
      $line = trim($line);
      if (preg_match('/^([^,]+)\,(.*)$/', $line, $matches)) {
        $key = $matches[1];
      }
      else {
        $invalid_maps[$line] = $line;
      }

      if (isset($key)) {
        if (isset($existing_keys[$group][$key])) {
          $duplicate_keys[$key] = $key;
        }
        else {
          $existing_keys[$group][$key] = $key;
        }
      }
    }

    if (!empty($invalid_maps)) {
      form_error($element, t('Each custom IP 2 Locale map must be in the format \'cc,lc\' (ISO 3166 2-character <em>Country Code</em> to Drupal <em>Language Code</em>) eg.') . '<br/>' . t('\'ie,en\' (<em>Ireland</em> to <em>English</em>)') . '<br/>' . t('\'us,en\' (<em>United States</em> to <em>English</em>)') . '<br/>' . t('The following mappings are invalid:') . theme('item_list', array('items' => $invalid_maps)));
    }

    if (!empty($duplicate_keys)) {
      form_error($element, t('Country Codes within the Custom IP 2 Locale mappings list must be unique. The following Country Codes have been used multiple times:') . theme('item_list', array('items' => $duplicate_keys)));
    }

  }

  return TRUE;
}