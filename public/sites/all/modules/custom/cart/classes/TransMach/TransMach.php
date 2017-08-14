<?php

class TransMach {
  const STATUS_CONFIRMED = 'CONFIRMED';

  const STATUS_REJECTED = 'REJECTED';

  public $transaction;

  public $braintreeTransaction;

  public $providerConfig;

  public $agentCode;

  public $agentId;

  public $externalReference;

  public $timestamp;

  public $booking = [];

  public $agentSummary = [];

  public $bookingSummary = [];

  public $bookingDetails = [];

  public $bookingRequest = [];

  public $bookingUri;

  public $apiKey;

  /**
   * ProviderLondon constructor.
   *
   * @param TicketingEngineTransaction $transaction
   * @param \Braintree\Transaction $braintreeTransaction
   */
  public function __construct(TicketingEngineTransaction $transaction, \Braintree\Transaction $braintreeTransaction) {
    $this->transaction          = $transaction;
    $this->braintreeTransaction = $braintreeTransaction;

    // create timestamp
    $this->timestamp = new \DateTime();

    // set external reference
    $this->externalReference = $this->transaction->getTransactionNumber();

    // load configuration data
    $this->loadConfig();

    // create booking
    $this->createBooking();
  }

  /**
   * @param array $content
   *
   * @return bool
   */
  public function getProviderTransactionStatus(array $content) {
    $status = $content['BookingResponse']['TransactionStatus']['Status'];

    if (self::STATUS_CONFIRMED === $status) {
      return TRUE;
    }

    return FALSE;
  }


  /**
   * @param array $content
   *
   * @return string
   */
  public function getProviderReference(array $content) {
    return $content['BookingResponse']['BookingDetails']['BookingReference'];
  }

  /**
   * @return string
   */
  public function serialize()
  {
    return json_encode($this->bookingRequest);
  }

  public function post(){
    $params = array(
      'headers' => array(
        'Content-Type'  => 'application/json',
      ),
      'data' => $this->serialize(),
      'method' => 'POST',
    );

    return drupal_http_request($this->bookingUri, $params);
  }

  protected function createBooking() {
    $this->setBookingData();

    $this->setAgentSummaryData();

    $this->setBookingSummaryData();

    $this->setBookingDetailData();

    // add data to booking
    $this->booking += [
      'AgentSummary'   => $this->agentSummary,
      'BookingSummary' => $this->bookingSummary,
      'BookingDetails' => $this->bookingDetails,
      'TotalPaid'      => $this->transaction->getTransactionTotal(),
    ];

    drupal_alter('cart_transmach_request', $this->booking, $this->transaction);

    // create booking request array
    $this->bookingRequest['BookingRequest'] = $this->booking;
  }

  protected function loadConfig() {
    $this->bookingUri = variable_get('transmach_request_url', '');
    $this->apiKey     = variable_get('transmach_api_key', '');
    $this->agentCode  = variable_get('transmach_agent_code', '');
    $this->agentId    = variable_get('transmach_agent_id', '');
  }

  private function setBookingDetailData() {
    $quantities    = $this->transaction->getQuantities();

    // create a new stdClass object to store booking details data
    $bookingDetail              = new \stdClass();
    $bookingDetail->error       = FALSE;
    $bookingDetail->productCode = $this->transaction->getProductCode();
    $bookingDetail->nAdult      = $quantities['adult'];
    $bookingDetail->nChild      = $quantities['child'];
    $bookingDetail->total       = $this->transaction->getQuantity();

    // add booking details data
    $this->addBookingDetailData('ProductGroupCode', $bookingDetail->productCode);
    $this->addBookingDetailData('nAdult', $bookingDetail->nAdult);
    $this->addBookingDetailData('nChild', $bookingDetail->nChild);
    $this->addBookingDetailData('Total', $bookingDetail->total);
  }

  private function setBookingSummaryData() {

    // add booking summary data
    $this->addBookingSummaryData('GivenName', $this->transaction->getFirstName());
    $this->addBookingSummaryData('LastName', $this->transaction->getLastName());
    $this->addBookingSummaryData('Email', $this->transaction->getEmail());

    // check if paypal or other non-standard transactions
    $last4 = $this->braintreeTransaction->creditCardDetails->last4 ?: '0000';
    $this->addBookingSummaryData('CardEndingWith', $last4);
    $this->addBookingSummaryData('DeliveryType', 'Print At Home');

    // get expiration date
    $expirationDate = time();
    $modifier = '+1 year';

    if($this->transaction->getTravelDate()){
      $expirationDate = $this->transaction->getTravelDate();
      $modifier = '+ '.$this->transaction->getOffering()->getNumberOfDays().' days';
    }
    $expirationDate = strtotime($modifier, $expirationDate);

    // continue adding booking summary data
    $this->addBookingSummaryData('ExpiryDate', date('Y-m-d', $expirationDate));
    $this->addBookingSummaryData('GroupBooking', 'YES');
    $this->addBookingSummaryData('TotalPassengers', $this->transaction->getQuantity());
  }

  private function setAgentSummaryData() {
    $this->addAgentSummaryData('AgentCode', $this->agentCode);
  }

  private function setBookingData() {
    // add booking request data
    $this->addBookingData('ApiKey', $this->apiKey);
    $this->addBookingData('AgentId', $this->agentId);
    $this->addBookingData('ExternalReference', $this->externalReference);
    $this->addBookingData('Timestamp', $this->timestamp);

    // create travel date
    $travelDate = $this->transaction->getTravelDate() ? $this->transaction->getTravelDate() : time();
    $this->addBookingData('TravelDate', date('Y-m-d',$travelDate));
  }

  /**
   * Set product code
   *
   * @param string $key
   * @param string $value
   */
  private function addBookingDetailData($key, $value) {
    $this->bookingDetails[$key] = $value;
  }

  /**
   * Set booking summary options
   *
   * @param string $key
   * @param string $value
   */
  private function addBookingSummaryData($key, $value) {
    $this->bookingSummary[$key] = $value;
  }

  /**
   * @param string $key
   * @param string $value
   */
  private function addAgentSummaryData($key, $value) {
    $this->agentSummary[$key] = $value;
  }

  /**
   * @param string $key
   * @param string $value
   */
  private function addBookingData($key, $value) {
    $this->booking[$key] = $value;
  }
}
