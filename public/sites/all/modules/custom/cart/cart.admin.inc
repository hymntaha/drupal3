<?php

function cart_admin_checkout_settings_form($form, &$form_state) {
  $form['buy_now_default_package'] = array(
    '#type'          => 'select',
    '#title'         => t('Buy Now Default Package'),
    '#options'       => package_get_package_options(),
    '#default_value' => variable_get('buy_now_default_package', ''),
  );

  $form['ticket_validity'] = array(
    '#type'          => 'textfield',
    '#title'         => t('Ticket Validity'),
    '#default_value' => variable_get('ticket_validity', ''),
  );

  $form['terms_and_conditions'] = array(
    '#type'          => 'text_format',
    '#title'         => t('Terms & Conditions'),
    '#default_value' => variable_get('terms_and_conditions', ''),
  );

  $form['email'] = array(
    '#type'        => 'fieldset',
    '#title'       => t('Checkout Emails'),
    '#collapsible' => TRUE,
    '#collapsed'   => TRUE,
  );

  foreach (language_list() as $lang) {
    $form['email']['checkout_email_' . $lang->language] = array(
      '#type'          => 'text_format',
      '#title'         => t('Checkout Email') . ' (' . $lang->language . ')',
      '#default_value' => variable_get('checkout_email_' . $lang->language, ''),
      '#description'   => 'Replacements: %first_name = First Name, %order_number = Order Number',
    );
  }

  $form['braintree'] = array(
    '#type'        => 'fieldset',
    '#title'       => 'Braintree',
    '#collapsible' => TRUE,
    '#collapsed'   => TRUE,
  );

  $form['braintree']['braintree_env'] = array(
    '#type'          => 'select',
    '#title'         => t('Braintree Environment'),
    '#options'       => array(
      'sandbox'    => t('Sandbox'),
      'production' => t('Production'),
    ),
    '#default_value' => variable_get('braintree_env', 'dev'),
  );

  $form['braintree']['braintree_merchant_id'] = array(
    '#type'          => 'textfield',
    '#title'         => t('Braintree Merchant ID'),
    '#default_value' => variable_get('braintree_merchant_id', ''),
  );

  $form['braintree']['braintree_public_key'] = array(
    '#type'          => 'textfield',
    '#title'         => t('Braintree Public Key'),
    '#default_value' => variable_get('braintree_public_key', ''),
  );

  $form['braintree']['braintree_private_key'] = array(
    '#type'          => 'textfield',
    '#title'         => t('Braintree Private Key'),
    '#default_value' => variable_get('braintree_private_key', ''),
  );

  $form['transmach'] = array(
    '#type'        => 'fieldset',
    '#title'       => 'TransMach',
    '#collapsible' => TRUE,
    '#collapsed'   => TRUE,
  );

  $form['transmach']['transmach_request_url'] = array(
    '#type'          => 'textfield',
    '#title'         => t('Request URL'),
    '#default_value' => variable_get('transmach_request_url', ''),
  );

  $form['transmach']['transmach_api_key'] = array(
    '#type'          => 'textfield',
    '#title'         => t('API Key'),
    '#default_value' => variable_get('transmach_api_key', ''),
  );

  $form['transmach']['transmach_agent_code'] = array(
    '#type'          => 'textfield',
    '#title'         => t('Agent Code'),
    '#default_value' => variable_get('transmach_agent_code', ''),
  );

  $form['transmach']['transmach_agent_id'] = array(
    '#type'          => 'textfield',
    '#title'         => t('Agent ID'),
    '#default_value' => variable_get('transmach_agent_id', ''),
  );

  $form['submit'] = array(
    '#type'  => 'submit',
    '#value' => t('Submit'),
  );

  return $form;
}

function cart_admin_checkout_settings_form_submit($form, &$form_state) {
  // Exclude unnecessary elements.
  form_state_values_clean($form_state);

  foreach ($form_state['values'] as $key => $value) {
    if (is_array($value)) {
      $value = $value['value'];
    }
    variable_set($key, $value);
  }

  drupal_set_message(t('The configuration options have been saved.'));
}