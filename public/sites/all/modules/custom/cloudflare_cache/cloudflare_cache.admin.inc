<?php
function cloudflare_cache_settings() {
  
  $build[] = drupal_get_form('cloudflare_cache_form');
  $build[] = drupal_get_form('cloudflare_cache_settings_form');
  
  return $build;
}

function cloudflare_cache_settings_form($form, &$form_state) {
  $form['settings'] = array(
    '#type' => 'fieldset',
    '#title' => 'Settings',
    '#collapsible' => true,
    '#collapsed' => true,
  );
 $form['settings']['cloudflare_cache_z'] = array(
		'#title' => 'z',
		'#default_value' => variable_get('cloudflare_cache_z', ''),
		'#required' => true,
		'#type' => 'textfield'
	);
  $form['settings']['cloudflare_cache_u'] = array(
		'#title' => 'u',
		'#default_value' => variable_get('cloudflare_cache_u', ''),
		'#required' => true,
		'#type' => 'textfield'
	);
  $form['settings']['cloudflare_cache_tkn'] = array(
		'#title' => 'tkn',
		'#default_value' => variable_get('cloudflare_cache_tkn', ''),
		'#required' => true,
		'#type' => 'textfield'
	);
  
  return system_settings_form($form);
}

function cloudflare_cache_form($form, &$form_state) {
  $form['cloudflare_cache'] = array(
    '#type' => 'fieldset',
    '#title' => 'Flush Cloudflare Cache',
    '#collapsible' => true,
    '#collapsed' => true,
  );
  $form['cloudflare_cache']['submit'] = array(
    '#type' => 'submit',
    '#value' => 'Flush Cloudflare Cache',
    '#prefix' => '<br />',
    '#attributes' => array('style' => 'border: 2px solid red;'),
  );
  
  return $form;
}

function cloudflare_cache_form_submit($form, &$form_state) {
  cloudflare_cache_flush();
}
