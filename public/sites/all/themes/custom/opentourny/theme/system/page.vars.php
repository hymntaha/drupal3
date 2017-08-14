<?php
/**
 * @file
 * page.vars.php
 */

/**
 * Implements hook_preprocess_page().
 *
 * @see page.tpl.php
 */
function opentourny_preprocess_page(&$variables) {

  // Add information about the number of sidebars.
  if (!empty($variables['page']['left_sidebar']) || !empty($variables['page']['sidebar_second'])) {
    $variables['content_column_classes_array'][] = 'col-sm-8';
  }

  $variables['hide_main_menu'] = FALSE;

  // Primary nav.
  $variables['primary_nav'] = FALSE;
  $variables['primary_nav_mobile'] = FALSE;

  if ($variables['main_menu']) {
    // Build links.
    $variables['primary_nav'] = menu_tree(variable_get('menu_main_links_source', 'main-menu'));
    // Provide default theme wrapper function.
    $variables['primary_nav']['#theme_wrappers'] = array('menu_tree__primary');
  }

  // Primary nav.
  if ($variables['primary_nav']) {
    $variables['primary_nav_mobile'] = $variables['primary_nav'];
    foreach($variables['primary_nav_mobile'] as $delta => $menu){
      if(isset($menu['#theme'])){
        $variables['primary_nav_mobile'][$delta]['#theme'] = 'menu_link__main_menu_mobile';
      }
    }
  }

  if(isset($variables['page']['navigation']['locale_language_content'])){
    $variables['language_switcher_mobile'] = $variables['page']['navigation']['locale_language_content'];
  }

  $variables['content_column_classes_array'][] = 'main-content';
  $variables['sidebar_column_classes_array'][] = 'col-sm-4 hidden-xs';

  $variables['buy_now_classes_array'][] = 'btn';
  $variables['buy_now_classes_array'][] = 'active';
  $variables['buy_now_classes_array'][] = 'otny-bg-'.variable_get('styles_buy_now_bg', 'red');
  $variables['buy_now_classes_array'][] = 'otny-color-'.variable_get('styles_buy_now_color', 'white');

  $variables['page_title_classes_array'] = array(
    'page-header',
    'otny-color-'.variable_get('styles_page_title_color', 'black'),
  );

  $variables['page_title_styles_array'] = array(
  	array('font-size' => variable_get('styles_page_title_size', '36').'px'),
  );

  $variables['main_container_classes_array'] = array(
  	'main-container',
  	'container',
  	'otny-color-'.variable_get('styles_body_color', 'black'),
  	'otny-color-link-'.variable_get('styles_link_color', 'blue'),
  );

  $variables['footer_classes_array'] = array(
  	'footer',
  	'container',
  	'otny-color-white',
  	'otny-color-link-white',
  );

  $variables['content_suffix'] = '';
  $variables['tracking_pixels'] = array();

  $facebook_pixel = array('#theme' => 'facebook_pixel', '#enabled' => TRUE);

  if(request_path() == 'order/your-order'){
    $facebook_pixel['#start_checkout'] = TRUE;
  }

  if(request_path() == 'order/your-details'){
    $facebook_pixel['#start_payment'] = TRUE;
  }

  if(cart_order_has_been_placed()){
    $transaction = TicketingEngineTransaction::load();
    $facebook_pixel['#order_total'] = $transaction->getTransactionTotal();

    if(module_exists('googleanalytics')){
      $variables['tracking_pixels']['ga_ecommerce']['#markup'] = theme('cart_google_analytics_ecommerce', array('transaction' => $transaction));
    }

    if(module_exists('commission_junction')){
      $variables['tracking_pixels']['commission_junction']['#markup'] = theme('commission_junction_conversion', array('transaction' => $transaction));
    }
  }

  $variables['tracking_pixels']['facebook'] = $facebook_pixel;

  if(isset($variables['node'])){
    switch ($variables['node']->type) {
      case 'how_it_works':
        $variables['title'] = '';
        break;
      case 'homepage':
        $variables['title'] = '';
        $variables['breadcrumb'] = '';
        drupal_add_js(drupal_get_path('theme', 'opentourny').'/js/homepage.js');
        break;
      case 'route_landing':
      case 'route':
        $variables['sharing_block_left'][] = array('#theme' => 'add_this');
        break;
      case 'package':
      $variables['sharing_block'][] = array('#theme' => 'add_this');
        break;
    }


    if(route_type_is_map_valid($variables['node']->type)){
      $variables['content_column_classes_array'] = array('col-md-6', 'main-content', 'map-main-content');
      $variables['sidebar_column_classes_array'] = array('col-md-6','leaflet-map-container');

      if(isset($_GET['mc'])){
        $variables['content_column_classes_array'][] = 'active';
      }
      else{
        $variables['sidebar_column_classes_array'][] = 'active';
      }

      $variables['title_prefix'][] = array('#markup' => '<div class="back-to-map-wrapper otny-color-link-'.variable_get('styles_link_color', 'blue' ).' visible-xs visible-sm"><a class="back-to-map" href="'.url('node/'.$variables['node']->nid).'"><span class="glyphicon glyphicon-chevron-left"></span> Map</a></div>');
      $variables['content_suffix'] = $variables['title_prefix'];
    }

    if($variables['node']->nid == variable_get('route_help_page_id', '')){
       $variables['title_prefix'][] = array('#markup' => '<div class="back-to-map-wrapper otny-color-link-'.variable_get('styles_link_color', 'blue' ).' visible-xs visible-sm"><a class="back-to-map" href="#" onclick="history.back();return false;"><span class="glyphicon glyphicon-chevron-left"></span> Map</a></div>');
      $variables['content_suffix'] = $variables['title_prefix'];      
    }

    $hide_main_menu = field_get_items('node',$variables['node'],'field_hide_main_menu');
    if(isset($hide_main_menu[0]['value']) && !empty($hide_main_menu[0]['value'])){
      $variables['hide_main_menu'] = TRUE;
    }

    if(in_array($variables['node']->type,array('homepage','how_it_works'))) {
      foreach($variables['main_container_classes_array'] as $delta => $class){
        if($class == 'container'){
          $variables['main_container_classes_array'][$delta] = 'container-fluid';
        }
      }
    }
  }

  $variables['full_width_page'] = FALSE;
  if(in_array('container-fluid', $variables['main_container_classes_array'])){
    $variables['full_width_page'] = TRUE;
  }
}

/**
 * Implements hook_process_page().
 *
 * @see page.tpl.php
 */
function opentourny_process_page(&$variables) {

  $variables['page_title_classes'] = implode(' ', $variables['page_title_classes_array']);

  $variables['page_title_styles'] = '';
  foreach($variables['page_title_styles_array'] as $style){
  	foreach($style as $name => $value){
  		$variables['page_title_styles'] .= $name.':'.$value.';';
  	}
  }

  $variables['buy_now_classes'] = implode(' ', $variables['buy_now_classes_array']);

  $variables['content_column_classes'] = implode(' ', $variables['content_column_classes_array']);

  $variables['sidebar_column_classes'] = implode(' ', $variables['sidebar_column_classes_array']);
  
  $variables['main_container_classes'] = implode(' ', $variables['main_container_classes_array']);
  
  $variables['footer_classes'] = implode(' ', $variables['footer_classes_array']);
}
