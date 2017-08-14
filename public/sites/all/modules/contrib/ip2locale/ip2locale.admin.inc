<?php
// $Id: ip2locale.admin.inc,v 1.1.2.11 2009/10/03 12:25:47 mrfelton Exp $

/**
 * @file
 * Admin functions for ip2locale module.
 */

function ip2locale_admin_settings($form, &$form_state) {
  $form = array();

  $form['ip2locale_debug'] = array(
    '#type' => 'checkbox',
    '#title' => t('Debug mode'),
    '#description' => t('With this setting enabled, an IP may be passed in through the query string using the ip2locale_debug parameter. This should not be used on production sites.'),
    '#default_value' => variable_get('ip2locale_debug', FALSE),
  );
  
  return system_settings_form($form);
}


function ip2locale_module_settings($form, &$form_state) {
  $form = array();
  $header = array(t('Feature'), t('Supported'), t('Notes'));
  
  $available_providers = ip2locale_provider_list('available', TRUE);
  $enabled_providers = ip2locale_provider_list('enabled');

  // Weights range from -delta to +delta, so delta should be at least half
  // of the amount of providers present. This makes sure all providers get
  // a unique weight.
  $weight_delta = round(count($available_providers) / 2);  

  $form['providers'] = array(
    '#type' => 'vertical_tabs',
  );
  $form['provider_status'] = array(
    '#tree' => TRUE,
  );

  $weight = 0;
  foreach ($available_providers as $provider => $provider_weight) {
    $weight++;
    $info = module_invoke($provider, 'ip2locale_info');
    
    // Provider weight.
    $available = !empty($available_providers[$provider]) ? t('yes') : t('no');
    $enabled = !empty($enabled_providers[$provider]) ? t('yes') : t('no');

    $enable_link = l(t('Enable'), 'ip2locale/provider/enable/' . $provider);
    $disable_link = l(t('Disable'), 'ip2locale/provider/disable/' . $provider);

    $ops = !empty($enabled_providers[$provider]) ? $disable_link : $enable_link;

    $form['provider_status'][$provider]['name'] = array(
      '#markup' => $info['#name'],
    );
    $form['provider_status'][$provider]['weight'] = array(
      '#type' => 'weight',
      '#delta' => $weight_delta,
      '#default_value' => $provider_weight,
      '#attributes' => array('class' => array('ip2locale-table-weight')),
    );
    $form['provider_status'][$provider]['enabled'] = array(
      '#markup' => $enabled,
    );
    $form['provider_status'][$provider]['ops'] = array(
      '#markup' => $ops,
    );
    
    // Provider settings.
    $form['providers'][$provider] = array(
      '#type' => 'fieldset',
      '#title' => $info['#name'],
      //'#description' => $info['#settings_description'],
      '#collapsible' => TRUE,
      '#collapsed' => TRUE,
      '#group' => 'providers',
      '#weight' => $weight,
      '#attached' => array(
        'js' => array(
          'vertical-tabs' => drupal_get_path('module', 'ip2locale') . '/ip2locale.js',
        ),
      ),
    );
    
    if (function_exists($provider . '_ip2locale_update')) {
      $form['providers'][$provider]['update'] = module_invoke($provider, 'ip2locale_update');
    }
    
    if (is_array($info['#supported_features']) && !empty($info['#supported_features'])) {
      $form['providers'][$provider]['supported_features'] = array(
        '#type' => 'fieldset',
        '#title' => t('Supported features'),
        '#description' => t('This is a list of the current state of support for the following features by %provider:', array('%provider' => $info['#name'])),
        '#collapsible' => TRUE,
        '#collapsed' => FALSE,
        '#weight' => 7,
      );
      $form['providers'][$provider]['supported_features']['features'] = array(
        '#type' => 'markup',
        '#markup' => theme('table', array('header' => $header, 'rows' => $info['#supported_features'])),
        '#attributes' => array('class' => array('clearfix')),
      );
    }
    $form['providers'][$provider][] = module_invoke($provider, 'ip2locale_settings');
    
  }
  
  $form = system_settings_form($form);

  $form['#theme'] = 'ip2locale_module_settings';
  $form['#submit'][] = 'ip2locale_module_settings_submit';
  
  return $form;
}

/**
 * @todo Please document this function.
 * @see http://drupal.org/node/1354
 */
function ip2locale_module_settings_submit($form, &$form_state) {
  foreach ($form_state['values']['provider_status'] as $provider => $details) {
    variable_set('ip2locale_provider_' . $provider . '_weight', $details['weight']);
  }
  drupal_set_message(t('The changes have been saved.'));
}

// Displays a paged list and overview of existing product fees.
/**
 * @todo Please document this function.
 * @see http://drupal.org/node/1354
 */
function theme_ip2locale_module_settings($variables) {
  $form = $variables['form'];
  
  $header = array(t('Provider'), t('Weight'), t('Enabled'), t('Actions'));

  if (count(element_children($form['provider_status'])) > 0) {
    foreach (element_children($form['provider_status']) as $module) {
      $row = array(
        drupal_render($form['provider_status'][$module]['name']),
        drupal_render($form['provider_status'][$module]['weight']),
        drupal_render($form['provider_status'][$module]['enabled']),
        drupal_render($form['provider_status'][$module]['ops']),
      );

      $rows[] = array(
        'data' => $row,
        'class' => array('draggable'),
      );
    }
  }
  else {
    $rows[] = array(
      array(
        'data' => t('No providers are available.'),
        'colspan' => 4,
      ),
    );
  }

  drupal_add_tabledrag('ip2locale-table', 'order', 'sibling', 'ip2locale-table-weight');
  $output = theme('table', array('header' => $header, 'rows' => $rows, 'attributes' => array('id' => 'ip2locale-table')));
  $output .= drupal_render_children($form);

  return $output;
}
