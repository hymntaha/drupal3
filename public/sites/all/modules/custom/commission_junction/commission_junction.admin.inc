<?php

function commission_junction_admin_settings_form($form, &$form_state){
  $form['cj_cid'] = array(
    '#title' => 'CID',
    '#type' => 'textfield',
    '#default_value' => variable_get('cj_cid', ''),
  );

  $form['cj_type'] = array(
    '#title' => 'TYPE',
    '#type' => 'textfield',
    '#default_value' => variable_get('cj_type', ''),
  );

  return system_settings_form($form);
}