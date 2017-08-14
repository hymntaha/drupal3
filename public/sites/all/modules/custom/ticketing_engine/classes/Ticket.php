<?php

class TicketingEngineTicket {
  public $id = 0;
  public $package = NULL;
  public $tour = NULL;
  public $adult_offering_id = 0;
  public $child_offering_id = 0;

  /**
   * @return int
   */
  public function getId() {
    return $this->id;
  }

  /**
   * @param int $id
   */
  public function setId($id) {
    $this->id = $id;
  }

  /**
   * @return object
   */
  public function getPackage(){
    return node_load($this->package);
  }

  public function setPackage($nid){
    $this->package = $nid;
  }

  /**
   * @return object
   */
  public function getTour(){
    if($this->tour){
      return taxonomy_term_load($this->tour);
    }

    if($tour = $this->getAdultOffering()->getTour()){
      $this->setTour($tour->tid);
      $this->save();

      return $tour;
    }

    return FALSE;
  }

  public function setTour($tid){
    $this->tour = $tid;
  }

  /**
   * @return string
   */
  public function getPackageCode(){
    $field = field_get_items('node',$this->getPackage(),'field_package_code');
    return isset($field[0]['value']) ? $field[0]['value'] : FALSE;
  }

  /**
   * @return string
   */
  public function getTourCodeSuffix(){
    return $this->getAdultOffering()->getTourCodeSuffix();
  }

  /**
   * @return string
   */
  public function getTourCode(){
    return $this->getAdultOffering()->getTourCode();
  }

  /**
   * @return string
   */
  public function getTourName(){
    global $language;

    if($this->getTour()){
      $tour_name = field_get_items('taxonomy_term',$this->getTour(),'name_field',$language->language);
      return isset($tour_name[0]['value']) ? $tour_name[0]['value'] : FALSE;
    }

    return $this->getAdultOffering()->getDescription();
  }

  public function getDurationLabel(){
    $num_days = substr($this->getTourCodeSuffix(), 0, 1);
    return t('!num_days Day Ticket', array('!num_days' => $num_days));
  }

  /**
   * @return boolean
   */
  public function isDated() {
    return $this->getAdultOffering()->isDated();
  }

  /**
   * @return int
   */
  public function getAdultOfferingId() {
    return $this->adult_offering_id;
  }

  /**
   * @param int $adult_offering_id
   */
  public function setAdultOfferingId($adult_offering_id) {
    $this->adult_offering_id = $adult_offering_id;
  }

  /**
   * @return int
   */
  public function getChildOfferingId() {
    return $this->child_offering_id;
  }

  /**
   * @param int $child_offering_id
   */
  public function setChildOfferingId($child_offering_id) {
    $this->child_offering_id = $child_offering_id;
  }

  /**
   * @return TicketingEngineOffering
   */
  public function getAdultOffering(){
    return TicketingEngineOffering::load($this->getAdultOfferingId());
  }

  /**
   * @return float
   */
  public function getAdultPrice() {
    return $this->getAdultOffering()->getPrice();
  }

  /**
   * @return string
   */
  public function getAdultPriceDisplay(){
    return $this->money($this->getAdultPrice());
  }

  /**
   * @return TicketingEngineOffering
   */
  public function getChildOffering(){
    return TicketingEngineOffering::load($this->getChildOfferingId());
  }

  /**
   * @return float
   */
  public function getChildPrice() {
    return $this->getChildOffering()->getPrice();
  }

  /**
   * @return string
   */
  public function getChildPriceDisplay(){
    return $this->money($this->getChildPrice());
  }

  /**
   * @param int $adults
   * @param int $children
   *
   * @return float
   */
  public function getTotal($adults, $children){
    return $this->getAdultPrice() * $adults + $this->getChildPrice() * $children;
  }

  /**
   * @param int $adults
   * @param int $children
   *
   * @return string
   */
  public function getTotalDisplay($adults, $children){
    return $this->money($this->getTotal($adults,$children));
  }

  /**
   * @return string
   */
  public function getCurrencySymbol(){
    return '$';
  }

  public function getCurrency(){
    return 'USD';
  }

  /**
   * @param float $price
   *
   * @return string
   */
  public function money($price){
    return $this->getCurrencySymbol().number_format($price,2);
  }

  public static function moneyConversion($price){
    $ticket = new TicketingEngineTicket();
    return $ticket->money($price);
  }

  public function save(){
    $update = array();

    if($this->getId()){
      $update = array('id');
    }

    return drupal_write_record('ticketing_engine_tickets', $this, $update);
  }

  /**
   * @param int $id
   *
   * @return bool|TicketingEngineTicket
   */
  public static function load($id){
    $result = db_query('SELECT * FROM ticketing_engine_tickets WHERE id=:id',array(':id' => $id));
    foreach($result as $row){
      $ticket = new TicketingEngineTicket();
      $ticket->setId($row->id);
      $ticket->setPackage($row->package);
      $ticket->setTour($row->tour);
      $ticket->setAdultOfferingId($row->adult_offering_id);
      $ticket->setChildOfferingId($row->child_offering_id);

      return $ticket;
    }

    return FALSE;
  }

  public static function deleteAll(){
    db_query('TRUNCATE ticketing_engine_tickets');
  }

}