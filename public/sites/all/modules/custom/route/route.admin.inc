<?php

function route_map_admin_settings_form($form,&$form_state){
	$form['route_nearest_point_distance'] = array(
		'#type' => 'textfield',
		'#title' => 'Nearest Point Distance (meters)',
		'#required' => TRUE,
		'#default_value' => variable_get('route_nearest_point_distance', 10000),
	);
	$form['route_help_page_id'] = array(
		'#type' => 'textfield',
		'#title' => 'Map mobile help page id',
		'#default_value' => variable_get('route_help_page_id', ''),
	);
	return system_settings_form($form);
}

function route_map_admin_settings_form_validate($form,&$form_state){
	if(!is_numeric($form_state['values']['route_nearest_point_distance']) || $form_state['values']['route_nearest_point_distance'] <= 0){
		form_set_error('route_nearest_point_distance',t('Point Distance must be a non-zero positive number.'));
	}
	if(!empty($form_state['values']['route_help_page_id'])){
		if(!is_numeric($form_state['values']['route_help_page_id']) || $form_state['values']['route_help_page_id'] <= 0){
			form_set_error('route_nearest_point_distance',t('Point Distance must be a non-zero positive number.'));
		}
	}
}