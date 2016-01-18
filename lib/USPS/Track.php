<?php
namespace USPS;

use USPS\API;

/**
 * USPS Tracking API
 */
class Track extends API {

  const API_NAME = 'TrackV2';

  private $tracking_number;

  private function getTrackingNumber()
  {
    return $this->tracking_number;
  }

  private function setTrackingNumber( $number )
  {
    $this->tracking_number = $number;
  }

  /**
   * Get delivery status
   */
  public function status( $tracking_number )
  {
    $this->setTrackingNumber( $tracking_number );

    $response = $this->sendRequest();

    $data = $this->parse( $response ); 

    $return = array(
      'summary' => $data['TrackSummary'],
      'details' => $data['TrackDetail'],
    );

    return $return;
  }

  /**
   * Parse the response
   */
  private function parse( $response )
  {
    $xml = simplexml_load_string( $response );

    $data = (array)$xml->TrackInfo;

    return $data;
  }

  /**
   * Build the request XML
   */
  protected function buildRequestXML()
  {
    $dom = new \DOMDocument( '1.0', 'UTF-8' );

    $root = $dom->createElement( 'TrackRequest' );
    $root->setAttribute( 'USERID', $this->getUserID() );
    $dom->appendChild( $root );

    $id = $dom->createElement( 'TrackID' );
    $id->setAttribute( 'ID', $this->getTrackingNumber() );

    $root->appendChild( $id );

    $dom->formatOutput = true;

    return $dom->saveXML();
  }

}