<?php
/**
 * @file
 * region.vars.php
 */

/**
 * Implements hook_preprocess_region().
 */
function opentourny_preprocess_region(&$variables) {
  switch ($variables['region']) {
  	case 'navigation':
    case 'footer':
      $variables['classes_array'][] = 'row';
      break;
  }
}
