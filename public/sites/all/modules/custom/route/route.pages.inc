<?php

function _route_poi_autocomplete($string) {
  global $language;

  $matches = array();

  $query = db_select('node', 'n')
    ->fields('n', array('nid'))
    ->condition('type', 'poi')
    ->condition('status', 1);
  $query->innerJoin('field_data_title_field', 't', 'n.nid = t.entity_id');
  $query->condition('t.language', $language->language)
        ->condition('t.title_field_value', '%' . $string . '%', 'LIKE');

  $result = $query->fields('t', array('title_field_value'))
                  ->orderBy('t.title_field_value')
                  ->execute();
  foreach ($result as $row) {
    $node                             = node_view(node_load($row->nid), 'line');
    $matches[$row->title_field_value] = $node['title_field'][0]['#markup'];
  }
  echo drupal_json_encode($matches);
  drupal_exit();
}

function route_get_bus_locations($json = FALSE, $zoom = 15) {
  $buses = array();

  $result = db_select('route_bus_locations','rbl')
    ->fields('rbl', array('latitude', 'longitude'))
    ->execute();

  foreach ($result as $row) {
    $buses[] = array(
      'lat'       => $row->latitude,
      'lon'       => $row->longitude,
      'icon'      => array(
        'iconUrl'  => '/' . drupal_get_path('theme', 'opentourny') . '/images/ico-bus.png',
        'iconSize' => $zoom > 13 ? array(
          'x' => 25,
          'y' => 67
        ) : array('x' => 12, 'y' => 33),
      ),
      'clickable' => FALSE,
    );
  }

  if ($json) {
    echo drupal_json_encode($buses);
    drupal_exit();
  }

  return $buses;
}