<?php

function opentourny_preprocess_field(&$variables, $hook){
  switch($variables['element']['#field_name']){
    case 'field_intro_title':
    case 'field_body_title':
    case 'field_show_city_title':
      $variables['classes_array'][] = 'homepage-section-title';
      break;
    case 'field_show_city_images':
      $variables['classes_array'][] = 'row';
      break;
      case 'field_rating_stars':
      break;
  }
}
