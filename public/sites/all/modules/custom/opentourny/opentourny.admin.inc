<?php

function opentourny_admin_settings_form($form, &$form_state){
	$color_options = opentourny_get_color_options();

	$form['styles'] = array(
		'#type' => 'fieldset',
		'#title' => 'Site Style Settings',
	);

	$form['styles']['styles_body_color'] = array(
		'#type' => 'select',
		'#title' => 'Default Font Color',
		'#options' => $color_options,
		'#default_value' => variable_get('styles_body_color', 'black'),
	);

	$form['styles']['styles_link_color'] = array(
		'#type' => 'select',
		'#title' => 'Default Link Font Color',
		'#options' => $color_options,
		'#default_value' => variable_get('styles_link_color', 'blue'),
	);

	$form['styles']['styles_page_title_color'] = array(
		'#type' => 'select',
		'#title' => 'Page Title Font Color',
		'#options' => $color_options,
		'#default_value' => variable_get('styles_page_title_color', 'black'),
	);

	$form['styles']['styles_page_title_size'] = array(
		'#type' => 'textfield',
		'#title' => 'Page Title Font Size (px)',
		'#default_value' => variable_get('styles_page_title_size', '36'),
	);

	$form['styles']['styles_buy_now_bg'] = array(
		'#type' => 'select',
		'#title' => 'Buy Now Background Color',
		'#options' => $color_options,
		'#default_value' => variable_get('styles_buy_now_bg', 'red'),
	);

	$form['styles']['styles_buy_now_color'] = array(
		'#type' => 'select',
		'#title' => 'Buy Now Font Color',
		'#options' => $color_options,
		'#default_value' => variable_get('styles_buy_now_color', 'white'),
	);

	$form['styles']['styles_button_bg'] = array(
		'#type' => 'select',
		'#title' => 'Default Button Background Color',
		'#options' => $color_options,
		'#default_value' => variable_get('styles_button_bg', 'white'),
	);

	$form['styles']['styles_button_color'] = array(
		'#type' => 'select',
		'#title' => 'Default Button Font Color',
		'#options' => $color_options,
		'#default_value' => variable_get('styles_button_color', 'blue'),
	);

	$form['phone_number'] = array(
		'#type' => 'textfield',
		'#title' => 'Header Phone Number Large',
		'#default_value' => variable_get('phone_number','855–OPEN TOUR'),
	);

	$form['phone_number_small'] = array(
		'#type' => 'textfield',
		'#title' => 'Header Phone Number Small',
		'#default_value' => variable_get('phone_number_small','673-6868'),
	);

	$form['phone_number_mobile'] = array(
		'#type' => 'textfield',
		'#title' => 'Header Phone Number Mobile',
		'#default_value' => variable_get('phone_number_mobile','855-673-6868'),
	);

    $form['find_us_url'] = array(
      '#type' => 'textfield',
      '#title' => t('Find Us Header Link'),
      '#default_value' => variable_get('find_us_url', ''),
    );

	return system_settings_form($form);
}

function opentourny_admin_settings_form_validate($form, &$form_state){
	if(!is_numeric($form_state['values']['styles_page_title_size']) || $form_state['values']['styles_page_title_size'] <= 0){
		form_set_error('styles_page_title_size',t('Page title font size must be a positive number.'));
	}
}