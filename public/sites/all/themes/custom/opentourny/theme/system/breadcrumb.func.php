<?php
/**
 * @file
 * breadcrumb.func.php
 */

/**
 * Overrides theme_breadcrumb().
 *
 * Print breadcrumbs as an ordered list.
 */
function opentourny_breadcrumb($variables) {
  $output = '';
  $breadcrumb = $variables['breadcrumb'];

  // Determine if we are to display the breadcrumb.
  $bootstrap_breadcrumb = theme_get_setting('bootstrap_breadcrumb');
  if (($bootstrap_breadcrumb == 1 || ($bootstrap_breadcrumb == 2 && arg(0) == 'admin'))) {
    if(empty($breadcrumb)){
      $breadcrumb[] = '&nbsp;';
    }
    else{
      $breadcrumb_count = count($breadcrumb);
      $breadcrumb[$breadcrumb_count-1]['class'][] = 'otny-color-'.variable_get('styles_body_color', 'black');
    }
    $output = theme('item_list', array(
      'attributes' => array(
        'class' => array('breadcrumb'),
      ),
      'items' => $breadcrumb,
      'type' => 'ol',
    ));
  }
  return $output;
}
