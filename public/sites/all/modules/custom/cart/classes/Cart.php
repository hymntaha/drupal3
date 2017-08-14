<?php

class Cart {
  private $package;
  private $ticket = FALSE;
  private $display_tour = '';
  private $adult = 0;
  private $children = 0;
  private $date = NULL;
  private $line_items = array();

  /**
   * @return mixed
   */
  public function getPackage() {
    return node_load($this->package);
  }

  /**
   * @param mixed $package
   */
  public function setPackage($package) {
    $this->package = $package;
  }

  /**
   * @return TicketingEngineTicket
   */
  public function getTicket() {
    return $this->ticket;
  }

  /**
   * @param TicketingEngineTicket $ticket
   */
  public function setTicket($ticket) {
    $this->ticket = $ticket;
  }

  /**
   * @return string
   */
  public function getDisplayTour() {
    return $this->display_tour;
  }

  /**
   * @param string $display_tour
   */
  public function setDisplayTour($display_tour) {
    $this->display_tour = $display_tour;
  }

  /**
   * @return int
   */
  public function getAdult() {
    return $this->adult;
  }

  /**
   * @param int $adult
   */
  public function setAdult($adult) {
    $this->adult = $adult;
  }

  /**
   * @return int
   */
  public function getChildren() {
    return $this->children;
  }

  /**
   * @param int $children
   */
  public function setChildren($children) {
    $this->children = $children;
  }

  /**
   * @param string $format
   *
   * @return null|int|string
   */
  public function getDate($format = 'Y-m-d') {
    if($this->date){
      return date($format,strtotime($this->date));
    }

    return $this->date;
  }

  /**
   * @param string $date
   */
  public function setDate($date = NULL) {
    $this->date = $date;
  }

  /**
   * @return CartLineItem[]
   */
  public function getLineItems() {
    return $this->line_items;
  }

  /**
   * @param CartLineItem $line_item
   *
   * @return bool
   */
  public function addLineItem($line_item) {
    if(!$this->getLineItemOfType($line_item->getType())){
      $this->line_items[] = $line_item;

      $this->save();

      return TRUE;
    }

    return FALSE;
  }

  /**
   * @param string $type
   *
   * @return CartLineItem|bool
   */
  public function removeLineItemOfType($type) {
    foreach($this->getLineItems() as $delta => $line_item){
      if($type == $line_item->getType()){
        unset($this->line_items[$delta]);

        $this->save();

        return $line_item;
      }
    }

    return FALSE;
  }

  public function getLineItemOfType($type){
    foreach($this->getLineItems() as $line_item){
      if($type == $line_item->getType()){
        return $line_item;
      }
    }

    return FALSE;
  }

  /**
   * @return bool
   */
  public function canCheckout(){
    if($this->getTicket()){
      if($this->getAdult() || $this->getChildren()){
        if($this->getTicket()->isDated() && $this->getDate()){
          return TRUE;
        }
        else if(!$this->getTicket()->isDated()){
          return TRUE;
        }
      }
    }

    return FALSE;
  }

  /**
   * @return float
   */
  public function getAdultTotal(){
    return $this->getTicket()->getAdultPrice() * $this->getAdult();
  }

  /**
   * @return string
   */
  public function getAdultTotalDisplay(){
    return $this->getTicket()->money($this->getAdultTotal());
  }

  /**
   * @return float
   */
  public function getChildTotal(){
    return $this->getTicket()->getChildPrice() * $this->getChildren();
  }

  /**
   * @return string
   */
  public function getChildTotalDisplay(){
    return $this->getTicket()->money($this->getChildTotal());
  }

  /**
   * @return int
   */
  public function getQuantity(){
    return $this->getAdult() + $this->getChildren();
  }

  /**
   * @return float
   */
  public function getLineItemTotal(){
    $total = 0;

    foreach($this->getLineItems() as $line_item){
      $total += $line_item->getAmount();
    }

    return $total;
  }

  /**
   * @return float
   */
  public function getSubtotal(){
    return $this->getAdultTotal() + $this->getChildTotal();
  }

  /**
   * @return string
   */
  public function getSubtotalDisplay(){
    return $this->getTicket()->money($this->getSubtotal());
  }

  /**
   * @return float
   */
  public function getTotal(){
    return $this->getSubtotal() + $this->getLineItemTotal();
  }

  /**
   * @return string
   */
  public function getTotalDisplay(){
    return $this->getTicket()->money($this->getTotal());
  }

  /**
   * @return string
   */
  public function getDateDisplay(){
    return $this->getDate() ? $this->getDate('m/d/Y') : 'Open';
  }

  public function save(){
    $_SESSION['cart'] = serialize($this);
  }

  /**
   * @return \Cart
   */
  public static function load(){
    if(isset($_SESSION['cart'])){
      return unserialize($_SESSION['cart']);
    }
    else{
      return new Cart();
    }
  }

  public static function delete(){
    if(isset($_SESSION['cart'])){
      unset($_SESSION['cart']);
    }
  }
}