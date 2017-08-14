<?php

class Checkout {
  const PAYMENT_METHOD_CC = 'credit_card';
  const PAYMENT_METHOD_PAYPAL = 'paypal';

  private $cart = NULL;
  private $first_name = '';
  private $last_name = '';
  private $email = '';
  private $mailing_list = FALSE;
  private $payment = NULL;
  private $braintreeTransaction = NULL;
  private $errors = array();

  /**
   * Checkout constructor.
   *
   * @param CheckoutPayment $payment
   */
  public function __construct($payment) {
    if (!empty($payment)) {
      $this->payment = $payment;
    }
    else {
      $this->payment = new CheckoutPaymentCreditCard();
    }
  }

  /**
   * @return Cart
   */
  public function getCart() {
    return $this->cart;
  }

  /**
   * @param Cart $cart
   */
  public function setCart($cart) {
    $this->cart = $cart;
  }

  /**
   * @return string
   */
  public function getFirstName() {
    return $this->first_name;
  }

  /**
   * @param string $first_name
   */
  public function setFirstName($first_name) {
    $this->first_name = $first_name;
  }

  /**
   * @return string
   */
  public function getLastName() {
    return $this->last_name;
  }

  /**
   * @param string $last_name
   */
  public function setLastName($last_name) {
    $this->last_name = $last_name;
  }

  /**
   * @return string
   */
  public function getEmail() {
    return $this->email;
  }

  /**
   * @param string $email
   */
  public function setEmail($email) {
    $this->email = $email;
  }

  /**
   * @return boolean
   */
  public function isMailingList() {
    return $this->mailing_list;
  }

  /**
   * @param boolean $mailing_list
   */
  public function setMailingList($mailing_list) {
    $this->mailing_list = $mailing_list;
  }

  /**
   * @return CheckoutPayment
   */
  public function getPayment() {
    return $this->payment;
  }

  /**
   * @param CheckoutPayment $payment
   */
  public function setPayment($payment) {
    $this->payment = $payment;
  }

  /**
   * @return Braintree\Transaction
   */
  public function getBraintreeTransaction() {
    return $this->braintreeTransaction;
  }

  /**
   * @param Braintree\Transaction $braintreeTransaction
   */
  public function setBraintreeTransaction($braintreeTransaction) {
    $this->braintreeTransaction = $braintreeTransaction;
  }

  /**
   * @return mixed
   */
  public function getNonce() {
    return $this->getPayment()->getNonce();
  }

  /**
   * @param mixed $nonce
   */
  public function setNonce($nonce) {
    $this->getPayment()->setNonce($nonce);
  }

  /**
   * @return array
   */
  public function getErrors() {
    return $this->errors;
  }

  /**
   * @param array $errors
   */
  public function setErrors($errors) {
    $this->errors = $errors;
  }

  public function clearPaymentInfo() {
    $this->getPayment()->clearPaymentInfo();
  }

  /**
   * @return string
   */
  public function getName() {
    return $this->getFirstName() . ' ' . $this->getLastName();
  }

  public function getPaymentMethodType() {
    if ($this->getPayment() instanceof CheckoutPaymentCreditCard) {
      return Checkout::PAYMENT_METHOD_CC;
    }
    else if ($this->getPayment() instanceof CheckoutPaymentPaypal) {
      return Checkout::PAYMENT_METHOD_PAYPAL;
    }

    return '';
  }

  /**
   * @return array
   */
  public function validate() {
    return $this->getPayment()->validate();
  }

  /**
   * @return array
   */
  public function toArray() {
    $checkout_array = array(
      'total'    => $this->getCart()->getTotal(),
      'currency' => $this->getCart()->getTicket()->getCurrency(),
    );

    return array_merge($checkout_array, $this->getPayment()->toArray());
  }

  public function toWebservicePostArray() {
    $product_codes  = array();
    $product_qty    = array();
    $product_prices = array();

    if ($this->getCart()->getAdult()) {
      $product_codes[]  = $this->getCart()
                               ->getTicket()
                               ->getAdultOffering()
                               ->getProductCode();
      $product_qty[]    = $this->getCart()->getAdult();
      $product_prices[] = $this->getCart()->getTicket()->getAdultPrice();
    }

    if ($this->getCart()->getChildren()) {
      $product_codes[]  = $this->getCart()
                               ->getTicket()
                               ->getChildOffering()
                               ->getProductCode();
      $product_qty[]    = $this->getCart()->getChildren();
      $product_prices[] = $this->getCart()->getTicket()->getChildPrice();
    }

    return array(
      'nonce'            => $this->getPayment()->getNonce(),
      'booking_details'  => array(
        'product_code'      => $product_codes,
        'quantity'          => $product_qty,
        'transaction_total' => $this->getCart()->getTotal(),
        'purchase_price'    => $product_prices,
        'travel_date'       => $this->getCart()
                                    ->getTicket()
                                    ->isDated() ? $this->getCart()
                                                       ->getDate() : NULL,
        'tour_code'         => $this->getCart()->getTicket()->getTourCode(),
      ),
      'customer_details' => array(
        'first_name' => $this->getFirstName(),
        'last_name'  => $this->getLastName(),
        'email'      => $this->getEmail(),
      ),
    );
  }

  public function canPay() {
    return $this->getFirstName() &&
           $this->getLastName() &&
           $this->getEmail() &&
           $this->getPayment()->canPay();
  }

  public function saveToMailingList() {
    if ($this->isMailingList()) {
      $email = db_query('SELECT email FROM transaction_emails WHERE email = :email', array(':email' => $this->getEmail()))->fetchField();
      if ($email === FALSE) {
        $record = array('email' => $this->getEmail());
        drupal_write_record('transaction_emails', $record);
      }
    }
  }

  public function displayErrors(){
    foreach($this->getErrors() as $code => $error){
      drupal_set_message($error, 'error');
    }

    $this->setErrors(array());
  }

  public function setFormDefaults(&$defaults) {
    $defaults['first_name']     = $this->getFirstName();
    $defaults['last_name']      = $this->getLastName();
    $defaults['email']          = $this->getEmail();
    $defaults['payment_method'] = $this->getPaymentMethodType();

    $this->getPayment()->setFromDefaults($defaults);
  }

  public function save() {
    $_SESSION['checkout'] = serialize($this);
  }

  public function generateBraintreeClientToken() {
    $this->braintreeStart();
    $client_token = Braintree\ClientToken::generate();
    $this->getPayment()->setClientToken($client_token);
  }

  /**
   * @return Braintree\Result\Successful|Braintree\Result\Error
   */
  public function braintreeSale() {
    $this->braintreeStart();
    $result = Braintree\Transaction::sale([
      'amount'             => $this->getCart()->getTotal(),
      'paymentMethodNonce' => $this->getNonce(),
      'options'            => [
        'submitForSettlement' => TRUE,
      ],
    ]);

    return $result;
  }

  public function braintreeVoid() {
    $this->braintreeStart();
    $transaction = $this->getBraintreeTransaction();

    switch ($transaction->status) {
      case 'settling':
      case 'settled':
        $result = \Braintree\Transaction::refund($transaction->id);
        break;

      default:
        $result = \Braintree\Transaction::void($transaction->id);
        break;
    }

    return $result;
  }

  public function braintreeSaleParseErrors($result) {
    $errors = [];
    if (!$result->success) {
      foreach ($result->errors->deepAll() as $error) {
        $errors[$error->code] = $error->message;
      }

      if(empty($errors)){
        $errors[] = $result->message;
      }

      watchdog('braintree', 'Sale Failed: !errors', array('!errors' => print_r($errors, TRUE)));
    }

    return $errors;
  }

  public static function delete() {
    if (isset($_SESSION['checkout'])) {
      unset($_SESSION['checkout']);
    }
  }

  /**
   * @return bool|\Checkout
   */
  public static function load() {
    if (isset($_SESSION['checkout'])) {
      return unserialize($_SESSION['checkout']);
    }
    else {
      return FALSE;
    }
  }

  /**
   * @param array $form_values
   *
   * @return \Checkout
   */
  public static function loadFromFormValues($form_values) {
    $payment = new CheckoutPaymentCreditCard();
    if ($form_values['payment_method'] == Checkout::PAYMENT_METHOD_PAYPAL) {
      $payment = new CheckoutPaymentPaypal();
    }
    $checkout = new Checkout($payment);

    $checkout->setCart(Cart::load());
    $checkout->setFirstName($form_values['first_name']);
    $checkout->setLastName($form_values['last_name']);
    $checkout->setEmail($form_values['email']);
    $checkout->setMailingList($form_values['mailing_list']);
    $checkout->getPayment()->loadFromFormValues($form_values);

    return $checkout;
  }

  private function braintreeStart() {
    module_load_include('php', 'cart', 'classes/Braintree/lib/Braintree');
    Braintree\Configuration::environment(variable_get('braintree_env', 'dev'));
    Braintree\Configuration::merchantId(variable_get('braintree_merchant_id', ''));
    Braintree\Configuration::publicKey(variable_get('braintree_public_key', ''));
    Braintree\Configuration::privateKey(variable_get('braintree_private_key', ''));
  }

}