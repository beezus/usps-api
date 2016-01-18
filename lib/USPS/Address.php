<?php
namespace USPS;

/**
 * A US postal address
 */
class Address {

  private $line1;
  private $line2 = NULL;
  private $city;
  private $state;
  private $zip;

  public function setLine1( $line1 )
  {
    $this->line1 = $line1;
  }

  public function getLine1()
  {
    return $this->line1;
  }

  public function setLine2( $line2 )
  {
    $this->line2 = $line2;
  }

  public function getLine2()
  {
    return $this->line2;
  }

  public function setCity( $city )
  {
    $this->city = $city;
  }

  public function getCity()
  {
    return $this->city;
  }

  public function setState( $state )
  {
    $this->state = $state;
  }

  public function getState()
  {
    return $this->state;
  }

  public function setZip( $zip )
  {
    $this->zip = $zip;
  }

  public function getZip()
  {
    return $this->zip;
  }

}