<?php

abstract class CheckoutPayment {
  private $clientToken;
  private $nonce;

  /**
   * @return mixed
   */
  public function getClientToken() {
    return $this->clientToken;
  }

  /**
   * @param mixed $clientToken
   */
  public function setClientToken($clientToken) {
    $this->clientToken = $clientToken;
  }

  /**
   * @return mixed
   */
  public function getNonce() {
    return $this->nonce;
  }

  /**
   * @param mixed $nonce
   */
  public function setNonce($nonce) {
    $this->nonce = $nonce;
  }

  abstract public function canPay();

  abstract public function clearPaymentInfo();

  abstract public function loadFromFormValues($form_values);

  abstract public function setFromDefaults(&$defaults);

  abstract public function toArray();

  abstract public function validate();
}