<?php

class TicketingEngineOffering {
  public $id = 0;
  public $product_code = '';
  public $package = '';
  public $description = '';
  public $dated = FALSE;
  public $number_of_days = 0;
  public $night = FALSE;
  public $bus = FALSE;
  public $cruise = FALSE;
  public $age_category = '';
  public $price = 0;
  public $created = 0;

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
   * @return string
   */
  public function getProductCode() {
    return $this->product_code;
  }

  /**
   * @param string $product_code
   */
  public function setProductCode($product_code) {
    $this->product_code = $product_code;
  }

  /**
   * @return string
   */
  public function getPackage() {
    return $this->package;
  }

  /**
   * @param string $package
   */
  public function setPackage($package) {
    $this->package = $package;
  }

  /**
   * @return string
   */
  public function getDescription() {
    return $this->description;
  }

  /**
   * @param string $description
   */
  public function setDescription($description) {
    $this->description = $description;
  }

  /**
   * @return boolean
   */
  public function isDated() {
    return $this->dated;
  }

  /**
   * @param boolean $dated
   */
  public function setDated($dated) {
    $this->dated = $dated;
  }

  /**
   * @return int
   */
  public function getNumberOfDays() {
    return $this->number_of_days;
  }

  /**
   * @param int $number_of_days
   */
  public function setNumberOfDays($number_of_days) {
    $this->number_of_days = $number_of_days;
  }

  /**
   * @return boolean
   */
  public function isNight() {
    return $this->night;
  }

  /**
   * @param boolean $night
   */
  public function setNight($night) {
    $this->night = $night;
  }

  /**
   * @return boolean
   */
  public function isBus() {
    return $this->bus;
  }

  /**
   * @param boolean $bus
   */
  public function setBus($bus) {
    $this->bus = $bus;
  }

  /**
   * @return boolean
   */
  public function isCruise() {
    return $this->cruise;
  }

  /**
   * @param boolean $cruise
   */
  public function setCruise($cruise) {
    $this->cruise = $cruise;
  }

  /**
   * @return string
   */
  public function getAgeCategory() {
    return $this->age_category;
  }

  /**
   * @param string $age_category
   */
  public function setAgeCategory($age_category) {
    $this->age_category = $age_category;
  }

  /**
   * @return int
   */
  public function getPrice() {
    return $this->price;
  }

  /**
   * @param int $price
   */
  public function setPrice($price) {
    $this->price = $price;
  }

  /**
   * @param string $format
   *
   * @return int|string
   */
  public function getCreated($format = '') {
    if (!empty($format)) {
      return date($format, $this->created);
    }
    return $this->created;
  }

  /**
   * @param null $created
   */
  public function setCreated($created) {
    $this->created = $created;
  }

  public function getTourCodeSuffix() {
    return $this->getNumberOfDays() . $this->isBus() . $this->isNight() . $this->isCruise();
  }

  public function getTourCode() {
    return $this->getPackage() . $this->isDated() . $this->getTourCodeSuffix();
  }

  /**
   * @return bool|object
   */
  public function getTour() {
    return ticketing_engine_load_tour_from_tour_code($this->getTourCode());
  }

  public function getTourDescription() {
    if ($tour = $this->getTour()) {
      $name = field_get_items('taxonomy_term', $tour, 'name_field');
      return $name[0]['value'];
    }

    return '';
  }

  public function getPackageNode() {
    $query  = new EntityFieldQuery();
    $result = $query->entityCondition('entity_type', 'node')
                    ->entityCondition('bundle', 'package')
                    ->fieldCondition('field_package_code', 'value', $this->getPackage())
                    ->execute();

    if (isset($result['node'])) {
      $nids = array_keys($result['node']);
      $nid  = reset($nids);
      if (!empty($nid)) {
        return node_load($nid);
      }
    }

    return FALSE;
  }


  public function getValidity() {
    /*
    if ($city = $this->getPackageNode()) {
      $validity = field_get_items('node', $city, 'field_ticket_validity');
      if (isset($validity[0]['value'])) {
        return $validity[0]['value'];
      }
    }
    */
    return '';
  }

  public function toArray() {
    return array(
      'product_code'         => $this->getProductCode(),
      'package_code'         => $this->getPackage(),
      'friendly_description' => $this->getDescription(),
      'dated'                => $this->isDated(),
      'number_of_days'       => $this->getNumberOfDays(),
      'day_bus'              => $this->isBus(),
      'night_bus'            => $this->isNight(),
      'cruise'               => $this->isCruise(),
      'age_category'         => $this->getAgeCategory(),
      'price'                => $this->getPrice(),
    );
  }

  public function save() {
    $update = array();

    if ($this->getId()) {
      $update = array('id');
    }

    drupal_write_record('ticketing_engine_offerings', $this, $update);
  }

  public function delete() {
    if ($this->getId()) {
      db_query('DELETE FROM ticketing_engine_offerings WHERE id = :id', array(':id' => $this->getId()));
      return TRUE;
    }

    return FALSE;
  }

  public static function deleteAll(){
    db_query('TRUNCATE ticketing_engine_offerings');
  }

  /**
   * @param $product_code
   *
   * @return TicketingEngineOffering|bool
   */
  public static function loadByProductCode($product_code) {
    if ($id = db_query('SELECT id FROM ticketing_engine_offerings WHERE product_code = :product_code', array(':product_code' => $product_code))->fetchField()) {
      return TicketingEngineOffering::load($id);
    }

    return FALSE;
  }

  /**
   * @param $id
   *
   * @return TicketingEngineOffering|bool
   */
  public static function load($id) {
    static $offerings = array();

    if (!empty($offerings[$id])) {
      return $offerings[$id];
    }

    $offerings = TicketingEngineOffering::loadMultiple(array($id));
    if (isset($offerings[$id])) {
      return $offerings[$id];
    }

    return FALSE;
  }

  /**
   * @param $ids
   *
   * @return TicketingEngineOffering()[]
   */
  public static function loadMultiple($ids) {
    $offerings = array();

    $result = db_select('ticketing_engine_offerings', 'o')
      ->fields('o')
      ->condition('id', $ids, 'IN')
      ->execute();

    foreach ($result as $row) {
      $offering = new TicketingEngineOffering();
      $offering->setId($row->id);
      $offering->setProductCode($row->product_code);
      $offering->setPackage($row->package);
      $offering->setDated($row->dated);
      $offering->setNumberOfDays($row->number_of_days);
      $offering->setBus($row->bus);
      $offering->setNight($row->night);
      $offering->setCruise($row->cruise);
      $offering->setDescription($row->description);
      $offering->setAgeCategory($row->age_category);
      $offering->setPrice($row->price);
      $offering->setCreated($row->created);

      $offerings[$offering->getId()] = $offering;
    }

    return $offerings;
  }

  public static function loadFromCSVRow($csv_row) {
    $csv_row = array_map('trim', $csv_row);

    $offering = new TicketingEngineOffering();
    $offering->setProductCode($csv_row[0]);
    $offering->setPackage($csv_row[1]);
    $offering->setDated(strtoupper($csv_row[2]) == 'D' ? TRUE : FALSE);
    $offering->setNumberOfDays($csv_row[3]);
    $offering->setBus($csv_row[4]);
    $offering->setNight($csv_row[5]);
    $offering->setCruise($csv_row[6]);
    $offering->setDescription($csv_row[7]);
    $offering->setAgeCategory($csv_row[8]);
    $offering->setPrice($csv_row[9]);

    return $offering;
  }

}