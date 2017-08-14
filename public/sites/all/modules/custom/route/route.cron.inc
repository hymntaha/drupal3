<?php

function route_cron_pull_bus_locations_callback(){
  $options = array(
    'headers' => array(
      'Content-Type' => 'application/x-www-form-urlencoded',
    ),
    'method' => 'POST',
    'data' => http_build_query(array(
      'grant_type' => 'password',
      'username' => '46aBES1!K4di',
      'password' => '7KvPP^v9DlHS',
    )),
  );

  $result = drupal_http_request('https://auth.networkfleet.com/token', $options);

  if($result->code == 200){
    $data = json_decode($result->data);

    $options = array(
      'headers' => array(
        'Authorization' => 'Bearer '.$data->access_token,
        'Accept' => 'application/vnd.networkfleet.api-v1+json',
        'Content-Type' => 'application/vnd.networkfleet.api-v1+json',
      )
    );

    $result = drupal_http_request('https://api.networkfleet.com/locations', $options);

    if($result->code == 200){
      db_query('TRUNCATE route_bus_locations');
      $data = drupal_json_decode($result->data);

      foreach($data['gpsMessage'] as $record){
        drupal_write_record('route_bus_locations', $record);
      }

      watchdog('bus_locations', 'Bus Locations Updated');
    }
  }

  drupal_exit();
}