<?php

/**
 * Overrides theme_button().
 */
function opentourny_button($variables) {
  if(isset($variables['#attributes']['class'][0]) && $variables['#attributes']['class'][0] == 'buy-tickets-button'){
  	if(!isset($variables['element']['#style_override']['bg']) || !$variables['element']['#style_override']['bg']){
  		$variables['element']['#attributes']['class'][] = 'otny-bg-'.variable_get('styles_button_bg', 'white');
  	}
  	if(!isset($variables['element']['#style_override']['color']) || !$variables['element']['#style_override']['color']){
  		$variables['element']['#attributes']['class'][] = 'otny-color-'.variable_get('styles_button_color', 'blue');
  		$variables['element']['#attributes']['class'][] = 'otny-border-color-'.variable_get('styles_button_color', 'blue');
  	}
  }
  return '<div class="button-wrapper"><button' . drupal_attributes($variables['element']['#attributes']) . '>' . $variables['element']['#value'] . "</button></div>\n";
}
