<?php

/**
 * Process input elements.
 */
function _opentourny_process_input(&$element, &$form_state) {
  $types = array(
    // Core.
    'password',
    'password_confirm',
    'textarea',
    'textfield',
    // HTML5.
    'email',
    // Webform module.
    'webform_email',
    // Elements module.
    'emailfield',
    'numberfield',
    'rangefield',
    'searchfield',
    'telfield',
    'urlfield',
  );
  if (!empty($element['#type']) && (in_array($element['#type'], $types) || ($element['#type'] === 'file' && empty($element['#managed_file'])))) {
    if(isset($element['#title'])){
      $element['#attributes']['placeholder'] = $element['#title'];
    }
  }
  return $element;
}