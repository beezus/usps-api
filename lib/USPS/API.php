<?php
namespace USPS;

/**
 * USPS API base
 */
class API {

  const TEST_API_ENDPOINT = 'http://production.shippingapis.com/ShippingAPITest.dll';
  const PRODUCTION_API_ENDPOINT = 'http://production.shippingapis.com/ShippingAPI.dll';
  const API_NAME = null; // Should be redefined by subclasses
  const USERAGENT = 'php-usps-client/0.1';

  public function __construct( $user_id )
  {
    $this->setUserId( $user_id );
  }

  public function testMode( $mode )
  {
    $this->test_mode = (bool) $mode;
  }

  private function isTestMode()
  {
    return $this->test_mode;
  }

  protected function getEndpoint()
  {
    return ( $this->isTestMode() ) ? self::TEST_API_ENDPOINT : self::PRODUCTION_API_ENDPOINT;
  }

  protected function getAPI()
  {
    return static::API_NAME;
  }

  protected function getUserID()
  {
    return $this->user_id;
  }

  protected function setUserID( $user_id )
  {
    $this->user_id = $user_id;
  }

  protected function getUseragent()
  {
    return self::USERAGENT;
  }

  /**
   * Setup the request
   */
  private function getRequestParameters()
  {
    return array(
      'API' => $this->getAPI(), 
      'XML' => $this->buildRequestXML()
    );
  }

  /**
   * Build the request XML - should be redefined by subclasses
   */
  protected function buildRequestXML() {}

  /**
   * cURL endpoint
   */
  protected function sendRequest()
  {
    $curl = curl_init();

    $CURL_OPTS = array(
      CURLOPT_URL => $this->getEndpoint(),
      CURLOPT_POSTFIELDS => http_build_query( $this->getRequestParameters(), null, '&' ),
      CURLOPT_USERAGENT => $this->getUseragent(),
      CURLOPT_CONNECTTIMEOUT => 30,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_TIMEOUT        => 60,
      CURLOPT_FRESH_CONNECT  => 1,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_RETURNTRANSFER => true,
    );

    curl_setopt_array( $curl, $CURL_OPTS );

    $resp = curl_exec( $curl );

    curl_close( $curl );

    return $resp;
  }

}