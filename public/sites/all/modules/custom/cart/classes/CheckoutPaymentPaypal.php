<?php

class CheckoutPaymentPaypal extends CheckoutPayment {
  private $readyToPay = FALSE;

  /**
   * @return boolean
   */
  public function isReadyToPay() {
    return $this->readyToPay;
  }

  /**
   * @param boolean $readyToPay
   */
  public function setReadyToPay($readyToPay) {
    $this->readyToPay = $readyToPay;
  }

  public function canPay() {
    return $this->isReadyToPay();
  }

  public function clearPaymentInfo() {
    $this->setReadyToPay(FALSE);
  }

  public function loadFromFormValues($form_values) {
    $this->setReadyToPay(TRUE);
  }

  public function setFromDefaults(&$defaults) {
    $defaults['paypal'] = $this->isReadyToPay();
  }

  public function toArray() {
    return array(
      'locale'          => $this->getLocale(),
      'payment_message' => t('Click the paypal button below to complete payment.'),
      'cancel'          => t('Cancel'),
    );
  }

  public function validate() {
    return array();
  }

  private function getLocale() {
    global $language;

    switch (strtolower($language->language)) {
      case 'fr':
        return 'fr_fr';
        break;
      case 'es':
        return 'es_es';
        break;
      case 'pt':
        return 'pt_pt';
        break;
      case 'de':
        return 'de_de';
        break;
      case 'zh-hans':
        return 'zh_hk';
        break;
    }

    return 'en_us';
  }
}