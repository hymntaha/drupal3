<?php

/**
 * Process variables for entity.tpl.php.
 */
function opentourny_preprocess_entity(&$variables, $hook) {
  $id = drupal_html_id($variables['title']);

  $variables['attributes_array']['id'] = $variables['entity_type'] . '-' . $id;

  $variables['theme_hook_suggestions'][] = $variables['entity_type'] . '__' .$id;

  unset($variables['attributes_array']['about']);
  unset($variables['attributes_array']['typeof']);

  $function = __FUNCTION__ . '_' . $variables['entity_type'];
  if (function_exists($function)) {
    $function($variables, $hook);
  }

}

function opentourny_preprocess_entity_slideshow(&$variables, $hook){
    $variables['classes_array'][] = 'carousel';
    $variables['classes_array'][] = 'slide';

    $variables['display_indicators'] = count($variables['content']['field_slideshow_image']['#items'])  > 1;
    $variables['background_slideshow'] = FALSE;
  
    if($variables['attributes_array']['id'] == 'slideshow-homepage-slideshow' || $variables['attributes_array']['id'] == 'slideshow-add-your-pass'){
      $variables['background_slideshow'] = TRUE;
      $variables['classes_array'][] = 'background-slideshow';

      foreach($variables['content']['field_slideshow_image']['#items'] as $delta => $item){
        $variables['content']['field_slideshow_image']['#items'][$delta]['image_url'] = file_create_url($item['uri']);
        $variables['content']['field_slideshow_image'][$delta] = array();
      }
    }

    if(isset($variables['content']['field_slideshow_promo']['#items'])){
      foreach($variables['content']['field_slideshow_promo']['#items'] as $delta => $promo){
        $variables['content']['field_slideshow_promo'][$delta]['#item']['attributes']['class'][] = 'img-promotion';
      }
    }
}

function opentourny_preprocess_entity_content_container(&$variables, $hook){
  $variables['classes_array'][] = 'otny-color-'.$variables['elements']['#entity']->field_font_color[LANGUAGE_NONE][0]['value'];
  if(isset($variables['elements']['#entity']->field_link_color[LANGUAGE_NONE][0]['value'])){
    $variables['classes_array'][] = 'otny-color-link-'.$variables['elements']['#entity']->field_link_color[LANGUAGE_NONE][0]['value'];
  }
  $variables['classes_array'][] = 'otny-bg-'.$variables['elements']['#entity']->field_background_color[LANGUAGE_NONE][0]['value'];

  if(!isset($variables['content']['field_cc_link'][0])){
    $variables['classes_array'][] = 'no-link';
  }
  else{
    $variables['attributes_array']['data-href'] = $variables['content']['field_cc_link'][0]['#element']['url'];
  }

  if(isset($variables['content']['field_cc_right_copy'][0])){
    $variables['content']['field_cc_left_copy'][0]['#prefix'] = '<div class="col-sm-6">';
    $variables['content']['field_cc_left_copy'][0]['#suffix'] = '</div>';

    $variables['content']['field_cc_right_copy'][0]['#prefix'] = '<div class="col-sm-6">';
    $variables['content']['field_cc_right_copy'][0]['#suffix'] = '</div>';
  }
  else{
    $variables['content']['field_cc_left_copy'][0]['#prefix'] = '<div class="col-xs-12">';
    $variables['content']['field_cc_left_copy'][0]['#suffix'] = '</div>';
  }

  if(isset($variables['content']['field_alt_title'][0]['#markup'])){
    $variables['title'] = $variables['content']['field_alt_title'][0]['#markup'];
  }

  $variables['background_url'] = FALSE;
  if(isset($variables['elements']['#entity']->field_background_image[LANGUAGE_NONE][0]['uri'])){
    $variables['background_url'] = file_create_url($variables['elements']['#entity']->field_background_image[LANGUAGE_NONE][0]['uri']);
  }
}
