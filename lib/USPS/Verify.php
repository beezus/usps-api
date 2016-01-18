<?php
namespace USPS;

use USPS\API;
use USPS\Address;

/**
 * USPS address verification API
 */
class Verify extends API {

  const API_NAME = 'Verify';

  private $dirty_address;
  private $clean_address;
  private $errors;

  public function dirtyAddress()
  {
    return $this->dirty_address;
  }

  public function cleanAddress()
  {
    return $this->clean_address;
  }

  public function errors()
  {
    return $this->errors ?: false;
  }

  /**
   * Verify the address
   *
   * @param Addres $address - the address to check
   */
  public function verify( Address $address )
  {
    $this->dirty_address = $address;

    $response = $this->sendRequest();

    $data = $this->parse( $response ); 

    $this->clean_address = new Address();
    $this->clean_address->setLine1( $data['Address2'] );
    $this->clean_address->setLine2( $data['Address1'] );
    $this->clean_address->setCity( $data['City'] );
    $this->clean_address->setState( $data['State'] );
    $this->clean_address->setZip( $data['Zip5'] . ( ($data['Zip4']) ? sprintf( '-%s', $data['Zip4'] ) : '' ) );

    return $this;
  }

  /**
   * Parse the response
   */
  private function parse( $response )
  {
    $xml = simplexml_load_string( $response );

    $data = (array)$xml->Address;

    if( is_object( $data['Error'] ) ) {
      $this->errors = (string)$data['Error']->Description;
    }

    // Needs address line 2
    if( isset( $data['ReturnText'] ) ) {
      $this->errors = str_replace('Default address: ', '', $data['ReturnText']);
    }

    return $data;
  }

  /**
   * Build the request XML
   */
  protected function buildRequestXML()
  {
    $dom = new \DOMDocument( '1.0', 'UTF-8' );

    $root = $dom->createElement( 'AddressValidateRequest' );
    $root->setAttribute( 'USERID', $this->getUserID() );
    $dom->appendChild( $root );

    $opt1 = $dom->createElement( 'IncludeOptionalElements', 'false' );
    $root->appendChild( $opt1 );

    $opt2 = $dom->createElement( 'ReturnCarrierRoute', 'false' );
    $root->appendChild( $opt2 );

    $address = $dom->createElement( 'Address' );
    $root->appendChild( $address );

    $recipient = $dom->createElement( 'FirmName' );
    $address->appendChild( $recipient );

    $line2 = $dom->createElement( 'Address1', $this->dirtyAddress()->getLine2() );
    $address->appendChild( $line2 );

    $line1 = $dom->createElement( 'Address2', $this->dirtyAddress()->getLine1() );
    $address->appendChild( $line1 );

    $city = $dom->createElement( 'City', $this->dirtyAddress()->getCity() );
    $address->appendChild( $city );

    $state = $dom->createElement( 'State', $this->dirtyAddress()->getState() );
    $address->appendChild( $state );

    $zip5 = $dom->createElement( 'Zip5', $this->dirtyAddress()->getZip() );
    $address->appendChild( $zip5 );

    $zip4 = $dom->createElement( 'Zip4' );
    $address->appendChild( $zip4 );

    $dom->formatOutput = true;

    return $dom->saveXML();
  }

}