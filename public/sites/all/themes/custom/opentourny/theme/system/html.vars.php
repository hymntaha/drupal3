<?php
/**
 * @file
 * html.vars.php
 *
 * @see html.tpl.php
 */

/**
 * Implements hook_preprocess_html().
 */
function opentourny_preprocess_html(&$variables) {
  $variables['viewport_options']   = '';
  $variables['html_classes_array'] = array();

  if ($node = menu_get_object()) {
     if ($node->type == 'homepage' || $node->type == 'how_it_works') {
      $variables['classes_array'][] = 'scroll-top';
      $variables['classes_array'][] = 'full-screen-header';
    }
    if (route_type_is_map_valid($node->type)) {
      if (!isset($_GET['mc'])) {
        $variables['classes_array'][]  = 'map-page';
        $variables['classes_array'][]  = 'no-footer';
        $variables['viewport_options'] = ', maximum-scale=1.0, user-scalable=0';
      }
    }
  }

  if (module_exists('mobile_detect')) {
    if (mobile_detect_get_object()->isMobile()) {
      $variables['html_classes_array'][] = 'mobile';
    }
  }
}

/**
 * Implements hook_process_html().
 */
function opentourny_process_html(&$variables) {
  $variables['html_classes'] = implode(' ', $variables['html_classes_array']);
}
