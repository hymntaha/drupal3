<?php

class TicketingEngineTransaction {
  private $braintree_transaction_id = '';
  private $transaction_number = 0;
  private $transaction_date = 0;
  private $transaction_total = 0;
  private $product_code = '';
  private $quantities = array();
  private $purchase_prices = array();
  private $tour_name = NULL;
  private $travel_date = NULL;
  private $status = '';
  private $status_updated = NULL;
  private $first_name = '';
  private $last_name = '';
  private $email = '';
  private $bar_code_base64 = '';
  private $provider_reference = '';
  private $data = array();

  /**
   * @return string
   */
  public function getBraintreeTransactionId() {
    return $this->braintree_transaction_id;
  }

  /**
   * @param string $braintree_transaction_id
   */
  public function setBraintreeTransactionId($braintree_transaction_id) {
    $this->braintree_transaction_id = $braintree_transaction_id;
  }

  /**
   * @return int
   */
  public function getTransactionNumber() {
    if ($this->transaction_number == 0) {
      $this->createTransactionNumber();
    }
    return $this->transaction_number;
  }

  /**
   * @param int $transaction_number
   */
  public function setTransactionNumber($transaction_number) {
    $this->transaction_number = $transaction_number;
  }

  /**
   * @param string $format
   *
   * @return int
   */
  public function getTransactionDate($format = '') {
    if ($format) {
      return date($format, $this->transaction_date);
    }

    return $this->transaction_date;
  }

  /**
   * @param int $transaction_date
   */
  public function setTransactionDate($transaction_date) {
    $this->transaction_date = $transaction_date;
  }

  /**
   * @return int
   */
  public function getTransactionTotal() {
    return $this->transaction_total;
  }

  /**
   * @param int $transaction_total
   */
  public function setTransactionTotal($transaction_total) {
    $this->transaction_total = $transaction_total;
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
   * @return array
   */
  public function getQuantities() {
    return $this->quantities;
  }

  /**
   * @param array $quantities
   */
  public function setQuantities($quantities) {
    $this->quantities = $quantities;
  }

  /**
   * @return array
   */
  public function getPurchasePrices() {
    return $this->purchase_prices;
  }

  /**
   * @param array $purchase_prices
   */
  public function setPurchasePrices($purchase_prices) {
    $this->purchase_prices = $purchase_prices;
  }

  /**
   * @return string
   */
  public function getTourName() {
    return $this->tour_name;
  }

  /**
   * @param string $tour_name
   */
  public function setTourName($tour_name) {
    $this->tour_name = $tour_name;
  }

  /**
   * @param string $format
   *
   * @return int|string
   */
  public function getTravelDate($format = '') {
    if ($format && $this->travel_date) {
      return date($format, $this->travel_date);
    }

    return $this->travel_date;
  }

  /**
   * @param int $travel_date
   */
  public function setTravelDate($travel_date) {
    $this->travel_date = $travel_date;
  }

  /**
   * @return string
   */
  public function getStatus() {
    return $this->status;
  }

  /**
   * @param string $status
   */
  public function setStatus($status) {
    $this->status = $status;
  }

  /**
   * @return null
   */
  public function getStatusUpdated() {
    return $this->status_updated;
  }

  /**
   * @param null $status_updated
   */
  public function setStatusUpdated($status_updated) {
    $this->status_updated = $status_updated;
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
   * @return string
   */
  public function getBarCodeBase64() {
    return $this->bar_code_base64;
  }

  /**
   * @param string $bar_code_base64
   */
  public function setBarCodeBase64($bar_code_base64) {
    $this->bar_code_base64 = $bar_code_base64;
  }

  /**
   * @return string
   */
  public function getProviderReference() {
    return $this->provider_reference;
  }

  /**
   * @param string $provider_reference
   */
  public function setProviderReference($provider_reference) {
    $this->provider_reference = $provider_reference;
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

  /**
   * @return array
   */
  public function getLineItems(){
    return isset($this->getData()['line_items']) ? $this->getData()['line_items'] : array();
  }

  /**
   * @param string $mode
   *
   * @return null|string
   * @throws \Exception
   */
  public function generatePDF($mode = 'I') {
    if (module_exists('cart')) {
      module_load_include('php', 'cart', 'mpdf60/mpdf');

      $mpf = new \mPDF();
      $mpf->WriteHTML(file_get_contents(drupal_get_path('module', 'cart') . '/css/voucher.css'), 1);
      $mpf->WriteHTML(theme('cart_transaction_voucher', array('transaction' => $this)), 2);

      if ($mode == 'S') {
        return $mpf->Output($this->getTransactionNumber() . '.pdf', $mode);
      }
      else {
        $mpf->Output($this->getTransactionNumber() . '.pdf', $mode);
      }
    }

    return NULL;
  }

  /**
   * @return bool|TicketingEngineOffering
   */
  public function getOffering() {
    return TicketingEngineOffering::loadByProductCode($this->getProductCode());
  }

  /**
   * @return bool|mixed
   */
  public function getPackage() {
    if ($offering = $this->getOffering()) {
      return $offering->getPackageNode();
    }

    return FALSE;
  }

  /**
   * @return string
   */
  public function getTransactionTotalDisplay() {
    return $this->money($this->getTransactionTotal());
  }

  /**
   * @return array
   */
  public function getPurchasePricesDisplay() {
    $prices_display = array();

    foreach ($this->getPurchasePrices() as $price) {
      $prices_display[] = $this->money($price);
    }

    return $prices_display;
  }

  /**
   * @return string
   */
  public function getValidity() {
    if ($offering = $this->getOffering()) {
      if ($offering->isDated() && $this->getTravelDate()) {
        if ($offering->getNumberOfDays() > 1) {
          return $this->getTravelDate('m/d/Y') . ' - ' . date('m/d/Y', strtotime('today +' . ($offering->getNumberOfDays() - 1) . ' days', $this->getTravelDate()));
        }
        else {
          return $this->getTravelDate('m/d/Y');
        }
      }
      else {
        return variable_get('ticket_validity', '');
      }
    }

    return '';
  }

  /**
   * @return int
   */
  public function getAdults() {
    return $this->getQuantityForAgeCategory('A');
  }

  /**
   * @return int
   */
  public function getChildren() {
    return $this->getQuantityForAgeCategory('C');
  }

  /**
   * @param string $age_category
   *
   * @return int
   */
  function getQuantityForAgeCategory($age_category) {
    $quantities = $this->getQuantities();

    $quantity = 0;
    switch ($age_category) {
      case 'A':
        if (isset($quantities['adult'])) {
          $quantity = $quantities['adult'];
        }
        break;
      case 'C':
        if (isset($quantities['child'])) {
          $quantity = $quantities['child'];
        }
        break;
    }

    return $quantity;
  }

  /**
   * @return string
   */
  public function getPackageLogo() {
    $render = array();
    if ($city = $this->getPackage()) {
      $render = field_view_field('node', $city, 'field_logo', array('label' => 'hidden'));
    }

    return render($render);
  }

  /**
   * @return string
   */
  public function getTermsText() {
    return variable_get('terms_and_conditions', '');
  }

  /**
   * @return bool|object
   */
  public function getTour() {
    return ticketing_engine_load_tour_from_tour_code($this->getOffering()
                                                          ->getTourCode());
  }

  /**
   * @param string $langcode
   *
   * @return string
   */
  public function getTourDescription($langcode = null) {
    if ($tour = $this->getTour()) {
      $name = field_get_items('taxonomy_term', $tour, 'name_field' , $langcode);
      return $name[0]['value'];
    }

    return '';
  }

  /**
   * @param string $langcode
   *
   * @return string
   */
  public function getTourRedeemText($langcode = null) {
    $render = array();
    if ($tour = $this->getTour()) {
      $render = field_view_field('taxonomy_term', $tour, 'description_field', array('label' => 'hidden'), $langcode);
    }

    return render($render);
  }

  /**
   * @param float $price
   *
   * @return string
   */
  public function money($price) {
    return '$' . number_format($price, 2);
  }

  public function getQuantity() {
    $quantities = $this->getQuantities();

    return $quantities['adult'] + $quantities['child'];
  }

  public function toArray() {
    return array(
      'transaction_number'       => $this->getTransactionNumber(),
      'braintree_transaction_id' => $this->getBraintreeTransactionId(),
      'transaction_date'         => $this->getTransactionDate(),
      'transaction_total'        => $this->getTransactionTotal(),
      'product_code'             => $this->getProductCode(),
      'tour_name'                => $this->getTourName(),
    );
  }

  public function save($session = FALSE) {
    if ($session) {
      $_SESSION['transaction'] = serialize($this);
    }

    if(!TicketingEngineTransaction::load($this->getTransactionNumber())){
      $record = new stdClass();
      $record->transaction_number = $this->getTransactionNumber();
      $record->braintree_transaction_id = $this->getBraintreeTransactionId();
      $record->transaction_date = $this->getTransactionDate();
      $record->transaction_total = $this->getTransactionTotal();
      $record->product_code = $this->getProductCode();
      $record->tour_name = $this->getTourName();
      $record->quantities = $this->getQuantities();
      $record->purchase_prices = $this->getPurchasePrices();
      $record->travel_date = $this->getTravelDate();
      $record->status = $this->getStatus();
      $record->status_updated = $this->getStatusUpdated();
      $record->first_name = $this->getFirstName();
      $record->last_name = $this->getLastName();
      $record->email = $this->getEmail();
      $record->barcode = $this->getBarCodeBase64();
      $record->provider_reference = $this->getProviderReference();
      $record->data = $this->getData();

      drupal_write_record('ticketing_engine_transactions', $record);
    }

  }

  public static function delete() {
    if (isset($_SESSION['transaction'])) {
      unset($_SESSION['transaction']);
    }
  }

  /**
   * @param bool|int $id
   *
   * @return bool|\TicketingEngineTransaction
   */
  public static function load($id = FALSE) {
    if ($id) {
      if ($row = db_query('SELECT * FROM ticketing_engine_transactions WHERE transaction_number = :id', array(':id' => $id))->fetchObject()) {
        $transaction = new TicketingEngineTransaction();
        $transaction->setTransactionNumber($row->transaction_number);
        $transaction->setBraintreeTransactionId($row->braintree_transaction_id);
        $transaction->setTransactionDate($row->transaction_date);
        $transaction->setTransactionTotal($row->transaction_total);
        $transaction->setProductCode($row->product_code);
        $transaction->setTourName($row->tour_name);
        $transaction->setQuantities(unserialize($row->quantities));
        $transaction->setPurchasePrices(unserialize($row->purchase_prices));
        $transaction->setTravelDate($row->travel_date);
        $transaction->setStatus($row->status);
        $transaction->setStatusUpdated($row->status_updated);
        $transaction->setFirstName($row->first_name);
        $transaction->setLastName($row->last_name);
        $transaction->setEmail($row->email);
        $transaction->setBarCodeBase64($row->barcode);
        $transaction->setProviderReference($row->provider_reference);
        $transaction->setData(unserialize($row->data));

        return $transaction;
      }
    }
    else if (isset($_SESSION['transaction'])) {
      return unserialize($_SESSION['transaction']);
    }

    return FALSE;
  }

  /**
   * @param Checkout $checkout
   *
   * @return TicketingEngineTransaction
   */
  public static function loadFromCheckout($checkout) {
    $product_code   = $checkout->getCart()
                               ->getTicket()
                               ->getAdultOffering()
                               ->getProductCode();
    $product_qty    = array(
      'adult' => 0,
      'child' => 0,
    );
    $product_prices = array(
      'adult' => $checkout->getCart()->getTicket()->getAdultPrice(),
      'child' => $checkout->getCart()->getTicket()->getChildPrice(),
    );

    if ($checkout->getCart()->getAdult()) {
      $product_qty['adult'] = $checkout->getCart()->getAdult();
    }

    if ($checkout->getCart()->getChildren()) {
      $product_qty['child'] = $checkout->getCart()->getChildren();
    }

    $transaction = new TicketingEngineTransaction();
    $transaction->setBraintreeTransactionId($checkout->getBraintreeTransaction()->id);
    $transaction->setTransactionTotal($checkout->getCart()->getTotal());
    $transaction->setProductCode($product_code);
    $transaction->setPurchasePrices($product_prices);
    $transaction->setQuantities($product_qty);
    $transaction->setTravelDate($checkout->getCart()->getDate());
    $transaction->setTourName($checkout->getCart()->getTicket()->getTourName());
    $transaction->setEmail($checkout->getEmail());
    $transaction->setFirstName($checkout->getFirstName());
    $transaction->setLastName($checkout->getLastName());

    $data = array();
    foreach($checkout->getCart()->getLineItems() as $line_item){
      $data['line_items'][] = $line_item->toArray();
    }

    $transaction->setData($data);

    return $transaction;
  }

  /**
   * @param TransMach $transmach
   * @param array $data
   */
  public function completeTransaction($transmach, $data) {
    $this->setTransactionDate(time());
    $this->setProviderReference($transmach->getProviderReference($data));

    module_load_include('php', 'cart', 'classes/QrCode/src/QrCode');

    // instantiate new qr code
    $qrCode = new \Endroid\QrCode\QrCode();

    // set qr code properties
    $qrCode
      ->setText($this->getProviderReference())
      ->setImageType('gif')
      ->setSize(100)
      ->setPadding(0)
      ->setErrorCorrection('high')
      ->setForegroundColor(['r' => 0, 'g' => 0, 'b' => 0, 'a' => 0])
      ->setBackgroundColor(['r' => 255, 'g' => 255, 'b' => 255, 'a' => 0])
      ->setLabelFontSize(8)
      ->create();

    $this->setBarCodeBase64($qrCode->getDataUri());
    $this->save(TRUE);
  }

  private function createTransactionNumber() {
    // create result variable
    $result = '';
    $length = 12;

    // generate random number
    for ($i = 0; $i < $length; ++$i) {
      $result .= mt_rand(0, 9);

      // check if result has a leading zero
      if (substr($result, 0, 1) == 0) {
        // remove leading zero
        $result = '';

        --$i;
      }
    }

    $transaction = TicketingEngineTransaction::load($result);
    if ($transaction !== FALSE) {
      $this->createTransactionNumber();
    }
    else {
      $this->setTransactionNumber($result);
    }
  }
}