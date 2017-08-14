<?php

function opentourny_preprocess_node(&$variables, $hook) {
  $variables['theme_hook_suggestions'][] = 'node__' . $variables['view_mode'];
  $variables['theme_hook_suggestions'][] = 'node__' . $variables['node']->type . '__' . $variables['view_mode'];
  $variables['theme_hook_suggestions'][] = 'node__' . $variables['node']->nid . '__' . $variables['view_mode'];

  if ($variables['view_mode'] == 'full_screen_content') {
    $variables['theme_hook_suggestions'] = array('node__' . $variables['view_mode']);
  }

  if ($variables['view_mode'] != 'teaser') {
    $variables['classes_array'][] = 'view-mode-' . $variables['view_mode'];
  }

  $function = __FUNCTION__ . '_' . $variables['node']->type;
  if (function_exists($function)) {
    $function($variables, $hook);
  }
}

function opentourny_preprocess_node_package(&$variables, $hook) {
  if ($variables['view_mode'] == 'teaser') {
    $variables['footer_classes_array'][] = 'node-footer';

    $variables['background_url'] = FALSE;
    if (isset($variables['node']->field_background_image[LANGUAGE_NONE][0]['uri'])) {
      $variables['background_url'] = file_create_url($variables['node']->field_background_image[LANGUAGE_NONE][0]['uri']);
    }

    $variables['background_url_mobile'] = FALSE;
    if (isset($variables['node']->field_teaser_mobile_bg_image[LANGUAGE_NONE][0]['uri'])) {
      $variables['background_url_mobile'] = file_create_url($variables['node']->field_teaser_mobile_bg_image[LANGUAGE_NONE][0]['uri']);
    }

    $variables['title'] = $variables['content']['title_field'][0]['#markup'];

    if (empty($variables['content']['field_teaser_price']['#items'])) {
      $variables['content']['field_teaser_price']['#items'] = array(
        array(
          'value'      => '',
          'format'     => NULL,
          'safe_value' => ''
        )
      );
    }
    else {
      $variables['content']['field_teaser_price'][0]['#markup'] .= '<span>/</span>';
    }

    $variables['content']['field_teaser_price'][0]['#prefix'] = '<a href="/order/your-order?package=' . $variables['node']->nid . '">';
    $variables['content']['field_teaser_price'][0]['#suffix'] = '<span>Buy Now<span class="teaser-price-arrow">&gt;</span></span></a>';

    $variables['footer_classes'] = implode(' ', $variables['footer_classes_array']);
  }
  else if ($variables['view_mode'] == 'line') {
    $variables['content']['field_feature_image'][0]['#item']['attributes']['class'][] = 'img-responsive';

    $variables['footer_classes_array'][] = 'node-footer';

    $variables['background_url'] = FALSE;

    $variables['background_url_mobile'] = FALSE;

    $attrs['element']['#attributes']['class'][]                     = 'feature-price';
    $variables['content']['field_adult_online_price'][0]['#markup'] = '<span ' . drupal_attributes($attrs['element']['#attributes']) . '>' . $variables['content']['field_adult_online_price'][0]['#markup'] . '</span>';

    $variables['title'] = $variables['content']['title_field'][0]['#markup'];

    $variables['footer_classes'] = implode(' ', $variables['footer_classes_array']);
  }
}

function opentourny_preprocess_node_route(&$variables, $hook) {
  if ($variables['view_mode'] == 'teaser') {
    $variables['classes_array'][] = 'otny-border-color-' . $variables['node']->field_background_color[LANGUAGE_NONE][0]['value'];

    $variables['title'] = t('View the @title', array('@title' => $variables['title']));
  }
  if ($variables['view_mode'] == 'full') {
    $variables['glance_title'] = t('The Tour in a Glance');

    $variables['glance_classes_array'][] = 'glance-container';
    $variables['glance_classes_array'][] = 'otny-color-' . $variables['node']->field_address_font_color[LANGUAGE_NONE][0]['value'];
    $variables['glance_classes_array'][] = 'otny-color-link-' . $variables['node']->field_address_font_color[LANGUAGE_NONE][0]['value'];
    $variables['glance_classes_array'][] = 'otny-bg-' . $variables['node']->field_address_bg_color[LANGUAGE_NONE][0]['value'];

    $variables['what_title'] = t("What You'll See");

    $variables['what_classes_array'][] = 'what-container';
    $variables['what_classes_array'][] = 'otny-color-' . $variables['node']->field_address_link_color[LANGUAGE_NONE][0]['value'];
    $variables['what_classes_array'][] = 'otny-color-link-' . $variables['node']->field_address_link_color[LANGUAGE_NONE][0]['value'];
    $variables['what_classes_array'][] = 'otny-bg-' . $variables['node']->field_button_background_color[LANGUAGE_NONE][0]['value'];

    $variables['glance_container_classes'] = implode(' ', $variables['glance_classes_array']);
    $variables['what_container_classes']   = implode(' ', $variables['what_classes_array']);
  }
}

function opentourny_preprocess_node_bus_stop(&$variables, $hook) {
  if ($variables['view_mode'] == 'full') {
    $variables['address_title'] = t('Stop Address');

    $variables['address_classes_array'][] = 'address-container';
    $variables['address_classes_array'][] = 'otny-color-' . $variables['node']->field_address_font_color[LANGUAGE_NONE][0]['value'];
    $variables['address_classes_array'][] = 'otny-color-link-' . $variables['node']->field_address_link_color[LANGUAGE_NONE][0]['value'];
    $variables['address_classes_array'][] = 'otny-bg-' . $variables['node']->field_address_bg_color[LANGUAGE_NONE][0]['value'];

    $variables['poi_title'] = t('Nearby Points of Interest');

    $variables['poi_classes_array'][] = 'poi-container';
    $variables['poi_classes_array'][] = 'otny-color-' . $variables['node']->field_font_color[LANGUAGE_NONE][0]['value'];
    $variables['poi_classes_array'][] = 'otny-color-link-' . $variables['node']->field_font_color[LANGUAGE_NONE][0]['value'];
    $variables['poi_classes_array'][] = 'otny-bg-' . $variables['node']->field_background_color[LANGUAGE_NONE][0]['value'];

    $variables['neighborhood_title'] = t('Neighborhood');

    $variables['address_classes'] = implode(' ', $variables['address_classes_array']);
    $variables['poi_classes']     = implode(' ', $variables['poi_classes_array']);
  }
}

function opentourny_preprocess_node_poi(&$variables, $hook) {
  if ($variables['view_mode'] == 'full') {
    $variables['poi_top_box_classes_array'][] = 'poi-top-box';
    $variables['poi_top_box_classes_array'][] = 'otny-color-' . $variables['node']->field_font_color[LANGUAGE_NONE][0]['value'];
    $variables['poi_top_box_classes_array'][] = 'otny-color-link-' . $variables['node']->field_link_color[LANGUAGE_NONE][0]['value'];
    $variables['poi_top_box_classes_array'][] = 'otny-bg-' . $variables['node']->field_background_color[LANGUAGE_NONE][0]['value'];

    $variables['neighborhood_title'] = t('Neighborhood');

    $variables['neighborhood_classes_array'][] = 'neighborhood-container';
    $variables['neighborhood_classes_array'][] = 'otny-color-' . $variables['node']->field_address_font_color[LANGUAGE_NONE][0]['value'];
    $variables['neighborhood_classes_array'][] = 'otny-color-link-' . $variables['node']->field_address_font_color[LANGUAGE_NONE][0]['value'];
    $variables['neighborhood_classes_array'][] = 'otny-bg-' . $variables['node']->field_address_bg_color[LANGUAGE_NONE][0]['value'];

    if (isset($variables['node']->field_address_link_color[LANGUAGE_NONE][0]['value'])) {
      $variables['content']['field_routes']['#prefix'] = '<div class="otny-color-link-' . $variables['node']->field_address_link_color[LANGUAGE_NONE][0]['value'] . '">';
      $variables['content']['field_routes']['#suffix'] = '</div>';
    }

    $variables['poi_top_box_classes']  = implode(' ', $variables['poi_top_box_classes_array']);
    $variables['neighborhood_classes'] = implode(' ', $variables['neighborhood_classes_array']);
  }
}

function opentourny_preprocess_node_neighborhood(&$variables, $hook) {
  if ($variables['view_mode'] == 'full') {
    $variables['poi_title'] = t('Nearby Points of Interest');

    $variables['poi_container_classes_array'][] = 'poi-container';
    $variables['poi_container_classes_array'][] = 'otny-color-' . $variables['node']->field_font_color[LANGUAGE_NONE][0]['value'];
    $variables['poi_container_classes_array'][] = 'otny-color-link-' . $variables['node']->field_font_color[LANGUAGE_NONE][0]['value'];
    $variables['poi_container_classes_array'][] = 'otny-bg-' . $variables['node']->field_background_color[LANGUAGE_NONE][0]['value'];

    $variables['bus_stop_title'] = t('Stops in ' . $variables['title']);

    $variables['bus_stop_container_classes_array'][] = 'bus-stop-container';
    $variables['bus_stop_container_classes_array'][] = 'otny-color-' . $variables['node']->field_address_font_color[LANGUAGE_NONE][0]['value'];
    $variables['bus_stop_container_classes_array'][] = 'otny-color-link-' . $variables['node']->field_address_link_color[LANGUAGE_NONE][0]['value'];
    $variables['bus_stop_container_classes_array'][] = 'otny-bg-' . $variables['node']->field_address_bg_color[LANGUAGE_NONE][0]['value'];

    $variables['poi_container_classes']      = implode(' ', $variables['poi_container_classes_array']);
    $variables['bus_stop_container_classes'] = implode(' ', $variables['bus_stop_container_classes_array']);
  }
}
