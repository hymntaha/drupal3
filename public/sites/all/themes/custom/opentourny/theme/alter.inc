<?php

function opentourny_form_alter(&$form, &$form_state, $form_id) {
  switch ($form_id) {
    case 'package_ticket_form':
    case 'cart_buy_tickets_form':
      $form['submit']['#value'] = '<span class="glyphicon glyphicon-shopping-cart"></span>'.t('Buy Now');
      $form['submit']['#attributes']['class'][] = 'otny-bg-'.variable_get('styles_buy_now_bg', 'red');
      $form['submit']['#attributes']['class'][] = 'otny-color-'.variable_get('styles_buy_now_color', 'white');
      break;
    case 'cart_step_1_form':
      $field_prefix = '<div class="col-sm-6">';
      $field_suffix = '</div>';

      foreach (element_children($form) as $element) {
        $form[$element]['#field_prefix'] = $field_prefix;
        $form[$element]['#field_suffix'] = $field_suffix;
      }

      $credit_card_image = theme('image', array(
        'path' => drupal_get_path('theme','opentourny').'/images/credit-card-logos.png',
        'attributes' => array(
          'class' => array('img-responsive'),
        )
      ));

      $form['submit']['#prefix'] = '<div class="row"><div class="col-sm-6">'.$credit_card_image.'</div>';
      $form['submit']['#suffix'] = '</div>';
      $form['submit']['#value']  = t('Check Out') . '<span class="glyphicon glyphicon-shopping-cart"></span>';
      break;
    case 'cart_step_2_form':
      $cart = Cart::load();

      $field_prefix = '<div class="col-sm-7">';
      $field_suffix = '</div>';

      foreach (element_children($form) as $element) {
        switch ($element) {
          case 'expiration_date_month':
          case 'expiration_date_year':
            $form[$element]['#field_prefix'] = '<div class="col-xs-6 col-sm-3">';
            $form[$element]['#field_suffix'] = $field_suffix;
            break;
          case 'cvv':
          case 'postal_code':
            $form[$element]['#field_prefix'] = '<div class="col-sm-3">';
            $form[$element]['#field_suffix'] = $field_suffix;
            break;
          case 'cart_table':
            $row_count = count($form[$element]['#rows']);
            if($row_count > 4){
              break;
            }
            $form[$element]['#rows'][$row_count-1]['class'][] = 'hidden-xs';
            $form[$element]['#rows'][]             = array(
              'class' => array('visible-xs','tour-row'),
              'data'  => array(
                'data' => array(
                  'class' => array('extra-info'),
                  'colspan' => 5,
                  'data'    => '<strong>YOUR ORDER:</strong> ' . $cart->getTicket()
                                                                      ->getTourName(),
                )
              )
            );
            $form[$element]['#rows'][] = array(
              'class' => array('visible-xs','date-row'),
              'data'  => array(
                'data' => array(
                  'class' => array('extra-info'),
                  'colspan' => 5,
                  'data'    => '<strong>DATE:</strong> ' . $cart->getDateDisplay(),
                )
              )
            );
            $form[$element]['#rows'][] = array(
              'class' => array('visible-xs', 'total-row', 'total-row-mobile'),
              'data'  => array(
                'data' => array(
                  'colspan' => 5,
                  'data'    => $form[$element]['#rows'][$row_count-1]['data'][1]['data'],
                ),
              ),
            );
            break;
          case 'coupon_wrapper':
            $form[$element]['#attributes']['class'][] = 'row';
            $form[$element]['coupon']['#prefix'] = '<div class="col-sm-7 coupon-field">';
            $form[$element]['coupon']['#field_prefix'] = '<div class="col-sm-7">';
            $form[$element]['coupon']['#field_suffix'] = $field_suffix;
            $form[$element]['coupon']['#suffix'] = $field_suffix;
            $form[$element]['coupon_submit']['#prefix'] = '<div class="col-sm-2 coupon-submit-field">';
            $form[$element]['coupon_submit']['#suffix'] = $field_suffix;
            break;
          case 'terms':
          case 'mailing_list':
            break;
          default:
            $form[$element]['#field_prefix'] = $field_prefix;
            $form[$element]['#field_suffix'] = $field_suffix;
            break;
        }
      }

      $form['your_details']['#prefix'] = '<div class="row"><div class="col-xs-12">';
      $form['your_details']['#suffix'] = '</div></div>';

      $form['payment_details']['#prefix'] = '<div class="row payment-details-row"><div class="col-sm-3">';
      $form['payment_details']['#suffix'] = '</div><div class="col-sm-3"><img class="img-responsive" src="/'.drupal_get_path('theme','opentourny').'/images/credit-card-logos.png" /></div></div>';

      $form['expiration_date_month']['#prefix'] = '<div class="form-group row">';
      $form['expiration_date_year']['#suffix']  = '</div>';

      break;
  }
}

/**
 * Implements hook_element_info_alter().
 */
function opentourny_element_info_alter(&$elements) {
  foreach ($elements as &$element) {
    if (!empty($element['#input'])) {
      $element['#process'][] = '_opentourny_process_input';
    }
  }
}