<?php

function hudson_admin_settings_form($form, &$form_state){
	$form['hudson_enabled'] = array(
		'#type' => 'checkbox',
		'#title' => 'Hudson Iframe Enabled',
		'#default_value' => variable_get('hudson_enabled', TRUE),
	);

	$form['hudson_url_en'] = array(
		'#type' => 'textfield',
		'#title' => 'Hudson Iframe English URL',
		'#default_value' => variable_get('hudson_url_en', ''),
	);

	$form['hudson_url_es'] = array(
		'#type' => 'textfield',
		'#title' => 'Hudson Iframe Spanish URL',
		'#default_value' => variable_get('hudson_url_es', ''),
	);

	$form['hudson_url_pt'] = array(
		'#type' => 'textfield',
		'#title' => 'Hudson Iframe Portuguese URL',
		'#default_value' => variable_get('hudson_url_pt', ''),
	);

	$form['hudson_url_fr'] = array(
		'#type' => 'textfield',
		'#title' => 'Hudson Iframe French URL',
		'#default_value' => variable_get('hudson_url_fr', ''),
	);

	$form['hudson_url_de'] = array(
		'#type' => 'textfield',
		'#title' => 'Hudson Iframe German URL',
		'#default_value' => variable_get('hudson_url_de', ''),
	);

	$form['hudson_url_zh-hans'] = array(
		'#type' => 'textfield',
		'#title' => 'Hudson Iframe Chinese URL',
		'#default_value' => variable_get('hudson_url_zh-hans', ''),
	);

	$form['hudson_url_linkname'] = array(
		'#type' => 'textfield',
		'#title' => 'Hudson Iframe URL LINKNAME',
		'#default_value' => variable_get('hudson_url_linkname', ''),
	);

	$form['hudson_bus_location_url'] = array(
		'#type' => 'textfield',
		'#title' => 'Hudson Bus Location Request URL',
		'#default_value' => variable_get('hudson_bus_location_url', ''),
	);

	$form['hudson_bus_location_enabled'] = array(
		'#type' => 'checkbox',
		'#title' => 'Enable Hudson Bus Location?',
		'#default_value' => variable_get('hudson_bus_location_enabled', FALSE),
	);

	$form['hudson_bus_location_test_mode'] = array(
		'#type' => 'checkbox',
		'#title' => 'Hudson Bus Location Test Mode',
		'#default_value' => variable_get('hudson_bus_location_test_mode', TRUE),
	);

	$form['hudson_child_age_range'] = array(
		'#type' => 'textfield',
		'#title' => 'Child Age Range',
		'#default_value' => variable_get('hudson_child_age_range', '3-17'),
	);

	$form['#submit'][] = 'hudson_admin_settings_form_submit';

	return system_settings_form($form);
}

function hudson_admin_settings_form_submit($form, &$form_state){

}