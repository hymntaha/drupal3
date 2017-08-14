<?php

function hudson_checkout_page(){

	if(variable_get('hudson_enabled', TRUE)){
		if(!empty($_SESSION['hudson_url'])){
			$url = $_SESSION['hudson_url'];
			return array(
				'#theme' => 'hudson_iframe',
				'#url' => $url,
			);
		}
		else{
			drupal_set_message(t('Your checkout session expired.'));
			drupal_goto('/');
		}
	}
	else{
		return array();
	}
}