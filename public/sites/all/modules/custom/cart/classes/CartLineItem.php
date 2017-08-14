<?php

class CartLineItem {
  private $type = '';
  private $label = '';
  private $amount = 0;
  private $data = array();

  /**
   * @return string
   */
  public function getType() {
    return $this->type;
  }

  /**
   * @param string $type
   */
  public function setType($type) {
    $this->type = $type;
  }

  /**
   * @return string
   */
  public function getLabel() {
    return $this->label;
  }

  /**
   * @param string $label
   */
  public function setLabel($label) {
    $this->label = $label;
  }

  /**
   * @return float
   */
  public function getAmount() {
    return $this->amount;
  }

  /**
   * @return float
   */
  public function getAmountDisplay() {
    if($this->amount < 0){
      return '- '.TicketingEngineTicket::moneyConversion($this->amount * -1);
    }

    return TicketingEngineTicket::moneyConversion($this->amount);
  }

  /**
   * @param float $amount
   */
  public function setAmount($amount) {
    $this->amount = $amount;
  }

  /**
   * @return array
   */
  public function getData() {
    return $this->data;
  }

  /**
   * @param array $data
   */
  public function setData($data) {
    $this->data = $data;
  }

  public function toArray(){
    return array(
      'type' => $this->getType(),
      'label' => $this->getLabel(),
      'amount' => $this->getAmount(),
      'data' => $this->getData(),
    );
  }

}