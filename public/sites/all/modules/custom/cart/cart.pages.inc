<?php

function cart_checkout_step_1() {
  global $language;
  $cart = Cart::load();

  $params = drupal_get_query_parameters();
  if(isset($params['package'])){
    if($node = node_load($params['package'])){
      if($node->type == 'package'){
        cart_clear_cart_checkout();

        $cart = Cart::load();
        $cart->setPackage($node->nid);
        $cart->save();
      }
    }
  }

  if (!$cart->getPackage()) {
    drupal_goto('');
  }

  $ajax_ticket_table_path = '/ajax/package-ticket-table/';
  $ajax_extra_info_path = '/ajax/ticketing-engine/tour-description/';

  if($language->language != 'en'){
    $ajax_ticket_table_path = '/'.$language->language.$ajax_ticket_table_path;
    $ajax_extra_info_path = '/'.$language->language.$ajax_extra_info_path;
  }

  drupal_add_js(array('package' => array('icon_path' => '/' . drupal_get_path('module', 'date_api') . '/images/calendar.png')), 'setting');
  drupal_add_js(array('cart' => array('ajax_ticket_table_path' => $ajax_ticket_table_path)), 'setting');
  drupal_add_js(array('cart' => array('ajax_extra_info_path' => $ajax_extra_info_path)), 'setting');
  drupal_add_js(drupal_get_path('module', 'package') . '/js/package.js');
  drupal_add_js(drupal_get_path('module', 'cart') . '/js/cart.js');

  return array(
    '#theme'        => 'cart_checkout',
    '#step_copy'    => t('Make your choice'),
    '#header'       => theme('cart_checkout_header', array('current_step' => 1)),
    '#form'         => drupal_get_form('cart_step_1_form'),
    '#ticket_table' => theme('package_ticket_table', array('package' => $cart->getPackage())),
    '#extra_info'   => '',
  );
}

function cart_checkout_step_2() {
  $cart = Cart::load();

  if (!$cart->canCheckout()) {
    drupal_goto('order/your-order');
  }

  $render['step_2'] = array(
    '#theme'     => 'cart_checkout',
    '#step_copy' => t('Complete your order'),
    '#header'    => theme('cart_checkout_header', array('current_step' => 2)),
    '#form'      => drupal_get_form('cart_step_2_form'),
  );

  $checkout = Checkout::load();

  if($checkout){
    $checkout->displayErrors();
  }

  if ($checkout && $checkout->canPay()) {
    $render['step_3'] = drupal_get_form('cart_checkout_step_3_form');
  }

  return $render;
}

function cart_checkout_step_4() {
  $transaction = TicketingEngineTransaction::load();

  if (!$transaction) {
    return MENU_ACCESS_DENIED;
  }

  return array(
    '#theme'     => 'cart_checkout',
    '#step_copy' => t('Order Details'),
    '#header'    => theme('cart_checkout_header', array('current_step' => 3)),
    '#form'      => array(
      '#theme'               => 'cart_checkout_confirmation',
      '#transaction_number'  => $transaction->getProviderReference(),
      '#transaction_details' => theme('cart_transaction_details', array('transaction' => $transaction)),
    ),
  );
}

function cart_checkout_voucher() {
  $transaction = TicketingEngineTransaction::load();

  if (!$transaction) {
    return MENU_ACCESS_DENIED;
  }

  admin_menu_suppress();

  //return theme('cart_transaction_voucher', array('transaction' => $transaction));

  drupal_add_http_header('Content-Type', 'application/pdf');

  $transaction->generatePDF();

  drupal_exit();

  return '';
}

function cart_step_1_form($form, &$form_state) {
  $cart = Cart::load();

  if (empty($form_state['input'])) {
    $form = package_ticket_form($form, $form_state, $cart->getPackage(), $cart);
  }
  else {
    $form = package_ticket_form($form, $form_state, $cart->getPackage());
  }

  $form['children']['#title'] .= cart_get_child_age_range($cart->getPackage());

  foreach($form['display_tour']['#options'] as $tour_code_suffix => $label){
    $num_days = substr($tour_code_suffix, 0, 1);
    $form['display_tour']['#options'][$tour_code_suffix] = t('!num_days Day Ticket', array('!num_days' => $num_days));
  }

  if (isset($form['total'])) {
    $form['total']['#type']   = 'item';
    $form['total']['#title']  = t('Total');
    $form['total']['#markup'] = $form['total']['#total'];
  }

  $form['package']['#weight']             = 0;
  $form['package']['#title_display']      = 'before';
  $form['adults']['#weight']              = 1;
  $form['adults']['#title_display']       = 'before';
  $form['children']['#weight']            = 2;
  $form['children']['#title_display']     = 'before';
  $form['display_tour']['#weight']        = 3;
  $form['display_tour']['#title_display'] = 'before';
  $form['display_tour']['#title']         = t('Duration');
  $form['dated']['#weight']               = 4;
  $form['dated']['#title_display']        = 'before';
  $form['dated']['#title']                = t('Validity');
  $form['dated']['#required']             = TRUE;
  $form['date']['#weight']                = 5;
  $form['date']['#title_display']         = 'before';
  $form['total']['#weight']               = 6;
  $form['submit']['#weight']              = 7;

  return $form;
}

function cart_step_1_form_validate($form, &$form_state) {
  if (isset($form_state['values']['dated'])) {
    $ticket = TicketingEngineTicket::load($form_state['values']['dated']);

    if ($ticket && $ticket->isDated()) {
      if (!is_array($form_state['values']['date']) && strtotime($form_state['values']['date']) < strtotime('today')) {
        form_set_error('date', t('Please choose a future date.'));
      }
    }
  }

  if ($form_state['values']['adults'] == 0 && $form_state['values']['children'] == 0) {
    form_set_error('', t('Please select the number of tickets.'));
  }
}

function cart_step_1_form_submit($form, &$form_state) {
  $cart = Cart::load();
  $cart->setPackage($form_state['values']['package']);
  $cart->setDisplayTour($form_state['values']['display_tour']);
  $cart->setTicket(TicketingEngineTicket::load($form_state['values']['dated']));
  $cart->setAdult($form_state['values']['adults']);
  $cart->setChildren($form_state['values']['children']);

  if (isset($form_state['values']['date']) && !is_array($form_state['values']['date'])) {
    $cart->setDate($form_state['values']['date']);
  }
  else {
    $cart->setDate();
  }

  $cart->save();

  $form_state['redirect'] = 'order/your-details';
}

function cart_step_2_form($form, &$form_state) {
  $cart = Cart::load();

  $defaults = array(
    'adults'                => isset($form_state['values']['adults']) ? $form_state['values']['adults'] : $cart->getAdult(),
    'children'              => isset($form_state['values']['children']) ? $form_state['values']['children'] : $cart->getChildren(),
    'first_name'            => '',
    'last_name'             => '',
    'email'                 => '',
    'billing_name'          => '',
    'card_number'           => '',
    'expiration_date_month' => '',
    'expiration_date_year'  => '',
    'cvv'                   => '',
    'postal_code'           => '',
    'paypal'                => '',
  );

  $checkout = Checkout::load();
  if ($checkout) {
    $checkout->setFormDefaults($defaults);
  }

  $defaults['payment_method'] = Checkout::PAYMENT_METHOD_CC;
  if (isset($form_state['values']['payment_method'])) {
    $defaults['payment_method'] = $form_state['values']['payment_method'];
  }
  else if ($checkout) {
    $defaults['payment_method'] = $checkout->getPaymentMethodType();
  }

  $header = array(
    array('data' => t('Your order'), 'class' => array('tour')),
    array('data' => t('Date'), 'class' => array('date')),
    array('data' => t('Title'), 'class' => array('title')),
    array('data' => t('Price'), 'class' => array('price')),
    array('data' => t('No. of tickets'), 'class' => array('num_tickets')),
    array('data' => t('Total'), 'class' => array('total')),
    array('data' => '', 'class' => array('edit')),
  );

  $rows[] = array(
    'class' => array('cart-row'),
    'data'  => array(
      array(
        'data'  => $cart->getTicket()
                        ->getAdultOffering()
                        ->getTourDescription(),
        'class' => array('tour')
      ),
      array(
        'data'  => $cart->getDateDisplay(),
        'class' => array('date')
      ),
      array('data' => t('Adult'), 'class' => array('title')),
      array(
        'data'  => $cart->getTicket()->getAdultPriceDisplay(),
        'class' => array('price')
      ),
      array('data' => $cart->getAdult(), 'class' => array('num_tickets')),
      array('data' => $cart->getAdultTotalDisplay(), 'class' => array('total')),
      array('data' => l('edit', 'order/your-order'), 'class' => array('edit')),
    ),
  );

  $rows[] = array(
    'class' => array('cart-row'),
    'data'  => array(
      array(
        'data'  => $cart->getTicket()
                        ->getChildOffering()
                        ->getTourDescription(),
        'class' => array('tour')
      ),
      array(
        'data'  => $cart->getDateDisplay(),
        'class' => array('date')
      ),
      array('data' => t('Child'), 'class' => array('title')),
      array(
        'data'  => $cart->getTicket()->getChildPriceDisplay(),
        'class' => array('price')
      ),
      array('data' => $cart->getChildren(), 'class' => array('num_tickets')),
      array('data' => $cart->getChildTotalDisplay(), 'class' => array('total')),
      array('data' => l('edit', 'order/your-order'), 'class' => array('edit')),
    ),
  );

  foreach($cart->getLineItems() as $line_item){
    $rows[] = array(
      'class' => array('cart-row'),
      'data' => array(
        array(
          'data' => $line_item->getLabel(),
          'colspan' => 3,
        ),
        array(
          'data'  => '',
          'class' => array('date'),
          'colspan' => 2,
        ),
        array(
          'data' => $line_item->getAmountDisplay(),
          'class' => array('total'),
          'colspan' => 2,
        ),
      ),
    );
  }

  $rows[] = array(
    'class' => array('total-row'),
    'data'  => array(
      array(
        'data'    => '',
        'colspan' => 5,
      ),
      array(
        'data'    => '<span class="total">' . t('Total') . ': ' . $cart->getTotalDisplay() . '</span>',
        'colspan' => 2,
      ),
    ),
  );

  $form['#prefix'] = '<div id="cart-step-2-form-ajax-wrapper">';
  $form['#suffix'] = '</div>';

  $form['cart_table'] = array(
    '#theme'  => 'table',
    '#header' => $header,
    '#rows'   => $rows,
    '#sticky' => FALSE,
    '#weight' => 0,
  );

  $form['your_details'] = array(
    '#markup' => '<h4>' . t('Your details') . '</h4>',
    '#weight' => 1,
  );

  $form['first_name'] = array(
    '#type'          => 'textfield',
    '#title'         => t('First Name'),
    '#required'      => TRUE,
    '#default_value' => $defaults['first_name'],
    '#attributes'    => array('placeholder' => ''),
    '#weight' => 2,
  );

  $form['last_name'] = array(
    '#type'          => 'textfield',
    '#title'         => t('Last Name'),
    '#required'      => TRUE,
    '#default_value' => $defaults['last_name'],
    '#attributes'    => array('placeholder' => ''),
    '#weight' => 3,
  );

  $form['email'] = array(
    '#type'          => 'textfield',
    '#title'         => t('E-mail (for sending your e-voucher)'),
    '#required'      => TRUE,
    '#default_value' => $defaults['email'],
    '#attributes'    => array('placeholder' => ''),
    '#weight' => 4,
  );

  $form['confirm_email'] = array(
    '#type'       => 'textfield',
    '#title'      => t('Confirm E-mail'),
    '#required'   => TRUE,
    '#attributes' => array('placeholder' => ''),
    '#weight' => 5,
  );

  $form['payment_details'] = array(
    '#markup' => '<h4>' . t('Payment details') . '</h4>',
    '#weight' => 15,
  );

  $form['payment_method'] = array(
    '#type'          => 'select',
    '#title'         => t('Payment Method'),
    '#options'       => array(
      Checkout::PAYMENT_METHOD_CC     => t('Credit card'),
      Checkout::PAYMENT_METHOD_PAYPAL => t('Paypal'),
    ),
    '#default_value' => $defaults['payment_method'],
    '#ajax'          => array(
      'callback' => 'cart_step_2_form_ajax_callback',
      'wrapper'  => 'cart-step-2-form-ajax-wrapper',
      'method'   => 'replace',
    ),
    '#weight' => 16,
  );

  if ($defaults['payment_method'] == Checkout::PAYMENT_METHOD_CC) {
    $form['billing_name'] = array(
      '#type'          => 'textfield',
      '#title'         => t('Cardholder Name'),
      '#required'      => TRUE,
      '#default_value' => $defaults['billing_name'],
      '#attributes'    => array('placeholder' => ''),
      '#weight' => 17,
    );

    $form['card_number'] = array(
      '#type'          => 'textfield',
      '#title'         => t('Card number'),
      '#required'      => TRUE,
      '#default_value' => $defaults['card_number'],
      '#attributes'    => array('placeholder' => ''),
      '#weight' => 18,
    );

    $form['expiration_date_month'] = array(
      '#type'          => 'select',
      '#title'         => t('Expiration date'),
      '#required'      => TRUE,
      '#default_value' => $defaults['expiration_date_month'],
      '#options'       => array(
        '01' => '01',
        '02' => '02',
        '03' => '03',
        '04' => '04',
        '05' => '05',
        '06' => '06',
        '07' => '07',
        '08' => '08',
        '09' => '09',
        '10' => '10',
        '11' => '11',
        '12' => '12',
      ),
      '#weight' => 19,
    );

    $years = array();
    $year  = date('Y');
    for ($i = 0; $i < 10; $i++) {
      $years[$year] = $year;
      $year++;
    }

    $form['expiration_date_year'] = array(
      '#title'         => '',
      '#type'          => 'select',
      '#required'      => TRUE,
      '#default_value' => $defaults['expiration_date_year'],
      '#options'       => $years,
      '#title_display' => 'invisible',
      '#weight' => 20,
    );

    $form['cvv'] = array(
      '#type'          => 'textfield',
      '#title'         => t('CVV (last three or four digits printed on the signature panel located at the back of your card)'),
      '#required'      => TRUE,
      '#default_value' => $defaults['cvv'],
      '#attributes'    => array('placeholder' => ''),
      '#weight' => 21,
    );

    $form['postal_code'] = array(
      '#type'          => 'textfield',
      '#title'         => t('ZIP/Postal code'),
      '#required'      => TRUE,
      '#default_value' => $defaults['postal_code'],
      '#attributes'    => array('placeholder' => ''),
      '#weight' => 22,
    );
  }
  else if ($defaults['payment_method'] == Checkout::PAYMENT_METHOD_PAYPAL) {
    $form['paypal'] = array(
      '#type'          => 'hidden',
      '#default_value' => $defaults['paypal'],
    );
  }

  $form['total'] = array(
    '#markup' => '<div class="order-total form-group">' . t('Total') . ': ' . $cart->getTotalDisplay() . '</div>',
    '#weight' => 45,
  );

  $form['terms'] = array(
    '#type'     => 'checkbox',
    '#title'    => t('I accept Open Loop New York\'s Terms & Conditions') . ' ' . l('(?)', 'terms-and-conditions', array('attributes' => array('target' => '_blank'))),
    '#required' => TRUE,
    '#weight' => 46,
  );

  $form['mailing_list'] = array(
    '#type'          => 'checkbox',
    '#title'         => t('I agree to receive e-mails from Open Loop New York about upcoming promotions and events.'),
    '#default_value' => TRUE,
    '#weight' => 47,
  );

  if ($cart->getTotal()) {
    $form['submit'] = array(
      '#type'  => 'submit',
      '#value' => t('Purchase Tickets'),
      '#weight' => 48,
    );
  }

  return $form;
}

function cart_step_2_form_ajax_callback($form, &$form_state) {
  return $form;
}

function cart_step_2_form_validate($form, &$form_state) {
  if ($form_state['values']['email'] != $form_state['values']['confirm_email']) {
    form_set_error('email', t('Emails do not match.'));
  }

  $checkout = Checkout::loadFromFormValues($form_state['values']);
  $validate = $checkout->validate();

  foreach ($validate as $field_name => $validated) {
    if (!$validated) {
      switch ($field_name) {
        case 'card_number':
          form_set_error('card_number', t('Invalid card number.'));
          break;
        case 'cvv':
          form_set_error('cvv', t('Invalid CVV.'));
          break;
        case 'expiration_date':
          form_set_error('expiration_date_month', t('Invalid Expiration Date.'));
          form_set_error('expiration_date_year', '');
          break;
      }
    }
  }
}

function cart_step_2_form_submit($form, &$form_state) {
  $checkout = Checkout::loadFromFormValues($form_state['values']);
  $checkout->save();
}

function cart_checkout_step_3_form($form, &$form_state) {
  $checkout = Checkout::load();
  $checkout->generateBraintreeClientToken();

  if (!$checkout->getPayment()->getClientToken()) {
    $checkout->clearPaymentInfo();
    $checkout->save();

    drupal_set_message(t('Unable to send payment to the webservice'), 'error');
    drupal_goto('order/your-details');
  }

  drupal_add_js('https://js.braintreegateway.com/v2/braintree.js', 'external');
  drupal_add_js(drupal_get_path('module', 'cart') . '/js/jquery.blockUI.js');
  drupal_add_js(drupal_get_path('module', 'cart') . '/js/cart_checkout_payment_' . $checkout->getPaymentMethodType() . '.js');
  drupal_add_js(array(
    'braintree' => array(
      'client_token' => $checkout->getPayment()
                                 ->getClientToken()
    )
  ), 'setting');
  drupal_add_js(array('checkout' => $checkout->toArray()), 'setting');

  $form['nonce'] = array(
    '#type' => 'hidden',
  );

  $form['error'] = array(
    '#type'          => 'hidden',
    '#default_value' => t('There was a problem processing your payment.'),
  );

  $form['submit'] = array(
    '#type'       => 'submit',
    '#value'      => 'Submit',
    '#attributes' => array('class' => array('element-invisible')),
  );

  return $form;
}

function cart_checkout_step_3_form_validate($form, &$form_state) {
  $redirect = FALSE;
  $checkout = Checkout::load();

  if (empty($form_state['values']['nonce'])) {
    drupal_set_message(t('There was a problem with the payment gateway.'), 'error');
    watchdog('Checkout', 'Payment failed: !message', array('!message' => $form_state['values']['error']));
    $redirect = TRUE;
  }

  if (!$redirect) {
    $checkout->setNonce($form_state['values']['nonce']);

    $result = $checkout->braintreeSale();
    $errors = $checkout->braintreeSaleParseErrors($result);

    if (!empty($errors)) {
      $checkout->setErrors($errors);
      $redirect = TRUE;
    }
    else {
      $checkout->setBraintreeTransaction($result->transaction);

      $transaction = TicketingEngineTransaction::loadFromCheckout($checkout);
      $transmach = new TransMach($transaction, $checkout->getBraintreeTransaction());

      if($response = $transmach->post()){
        if(!empty($response->data)){
          $data = json_decode($response->data, TRUE);
          if($transmach->getProviderTransactionStatus($data)){
            $transaction->completeTransaction($transmach, $data);
            $form_state['transaction'] = $transaction;
          }
          else{
            watchdog('TransMach', 'TransMach request error: !request -- !response', array('!request' => $transmach->serialize(), '!response' => print_r($response, TRUE)));
            $message = t('Unfortunately your booking could not be completed as the tour is not available or already fully booked.');
            if ($checkout->getCart()->getTicket()->isDated()) {
              $message = t('Unfortunately your booking could not be completed as the tour is not available or already fully booked. Please try with another date.');
            }

            $checkout->braintreeVoid();

            drupal_set_message($message, 'error');
            $redirect = TRUE;
          }
        }
        else{
          drupal_set_message(t('There was a problem with the booking engine.'), 'error');
          $redirect = TRUE;
        }
      }
      else{
        drupal_set_message(t('There was a problem with the booking engine.'), 'error');
        $redirect = TRUE;
      }
    }
  }

  if ($redirect) {
    if (isset($checkout) && $checkout) {
      $checkout->clearPaymentInfo();
      $checkout->save();
    }
    drupal_goto('order/your-details');
  }
}

function cart_checkout_step_3_form_submit($form, &$form_state) {
  $checkout = Checkout::load();
  $checkout->saveToMailingList();

  cart_clear_cart_checkout();

  /** @var TicketingEngineTransaction $transaction */
  $transaction = $form_state['transaction'];

  cart_send_transaction_email($transaction);

  $transaction->save(TRUE);

  $_SESSION['order_placed'] = TRUE;

  $form_state['redirect'] = 'order/confirmation';
}