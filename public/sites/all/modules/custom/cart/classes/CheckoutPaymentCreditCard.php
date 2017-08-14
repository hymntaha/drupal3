<?php

class CheckoutPaymentCreditCard extends CheckoutPayment {
  private $billingName;
  private $cardNumber;
  private $expirationDateMonth;
  private $expirationDateYear;
  private $cvv;
  private $billingPostalCode;

  /**
   * @return mixed
   */
  public function getBillingName() {
    return $this->billingName;
  }

  /**
   * @param mixed $billingName
   */
  public function setBillingName($billingName) {
    $this->billingName = $billingName;
  }

  /**
   * @return mixed
   */
  public function getCardNumber() {
    return $this->cardNumber;
  }

  /**
   * @param mixed $cardNumber
   */
  public function setCardNumber($cardNumber) {
    $this->cardNumber = $cardNumber;
  }

  /**
   * @return mixed
   */
  public function getExpirationDateMonth() {
    return $this->expirationDateMonth;
  }

  /**
   * @param mixed $expirationDateMonth
   */
  public function setExpirationDateMonth($expirationDateMonth) {
    $this->expirationDateMonth = $expirationDateMonth;
  }

  /**
   * @return mixed
   */
  public function getExpirationDateYear() {
    return $this->expirationDateYear;
  }

  /**
   * @param mixed $expirationDateYear
   */
  public function setExpirationDateYear($expirationDateYear) {
    $this->expirationDateYear = $expirationDateYear;
  }

  /**
   * @return mixed
   */
  public function getCvv() {
    return $this->cvv;
  }

  /**
   * @param mixed $cvv
   */
  public function setCvv($cvv) {
    $this->cvv = $cvv;
  }

  /**
   * @return mixed
   */
  public function getBillingPostalCode() {
    return $this->billingPostalCode;
  }

  /**
   * @param mixed $billingPostalCode
   */
  public function setBillingPostalCode($billingPostalCode) {
    $this->billingPostalCode = $billingPostalCode;
  }

  public function canPay() {
    return $this->getCardNumber() &&
           $this->getExpirationDateMonth() &&
           $this->getExpirationDateYear() &&
           $this->getCvv() &&
           $this->getBillingPostalCode();
  }

  public function clearPaymentInfo() {
    $this->setCardNumber('');
    $this->setExpirationDateMonth('');
    $this->setExpirationDateYear('');
    $this->setCvv('');
    $this->setBillingPostalCode('');
  }

  public function loadFromFormValues($form_values) {
    $this->setBillingName($form_values['billing_name']);
    $this->setCardNumber($form_values['card_number']);
    $this->setExpirationDateMonth($form_values['expiration_date_month']);
    $this->setExpirationDateYear($form_values['expiration_date_year']);
    $this->setCvv($form_values['cvv']);
    $this->setBillingPostalCode($form_values['postal_code']);
  }

  public function setFromDefaults(&$defaults) {
    $defaults['billing_name']          = $this->getBillingName();
    $defaults['card_number']           = $this->getCardNumber();
    $defaults['expiration_date_month'] = $this->getExpirationDateMonth();
    $defaults['expiration_date_year']  = $this->getExpirationDateYear();
    $defaults['cvv']                   = $this->getCvv();
    $defaults['postal_code']           = $this->getBillingPostalCode();
  }

  public function toArray() {
    return array(
      'card_number'           => $this->getCardNumber(),
      'name'                  => $this->getBillingName(),
      'expiration_date_month' => $this->getExpirationDateMonth(),
      'expiration_date_year'  => $this->getExpirationDateYear(),
      'cvv'                   => $this->getCvv(),
      'postal_code'           => $this->getBillingPostalCode(),
      'payment_message'       => t('Processing Payment... Please do not refresh or close the browser.'),
    );
  }

  public function validate() {
    return array(
      'card_number'     => $this->validate_card_number(),
      'expiration_date' => $this->validate_exp_date(),
      'cvv'             => $this->validate_cvv(),
    );
  }

  private function validate_card_number() {
    $number = $this->getCardNumber();

    if (!ctype_digit($number)) {
      return FALSE;
    }

    return $this->luhn_check($number);
  }

  private function validate_exp_date() {
    if ($this->getExpirationDateYear() < date('Y')) {
      return FALSE;
    }
    elseif ($this->getExpirationDateYear() == date('Y')) {
      if ($this->getExpirationDateMonth() < date('n')) {
        return FALSE;
      }
    }

    return TRUE;
  }

  private function validate_cvv() {
    $digits = array(3, 4);

    // Fail validation if it's non-numeric or an incorrect length.
    if (!is_numeric($this->getCvv()) || (count($digits) > 0 && !in_array(strlen($this->getCvv()), $digits))) {
      return FALSE;
    }

    return TRUE;
  }

  private function luhn_check($number) {

    // Strip any non-digits (useful for credit card numbers with spaces and hyphens)
    $number=preg_replace('/\D/', '', $number);

    // Set the string length and parity
    $number_length=strlen($number);
    $parity=$number_length % 2;

    // Loop through each digit and do the maths
    $total=0;
    for ($i=0; $i<$number_length; $i++) {
      $digit=$number[$i];
      // Multiply alternate digits by two
      if ($i % 2 == $parity) {
        $digit*=2;
        // If the sum is two digits, add them together (in effect)
        if ($digit > 9) {
          $digit-=9;
        }
      }
      // Total up the digits
      $total+=$digit;
    }

    // If the total mod 10 equals 0, the number is valid
    return ($total % 10 == 0) ? TRUE : FALSE;
  }
}
