<?php
namespace sbronsted;

/**
 * Class CaseNumber is use to represent at case number at UFDS
 */
class CaseNumber implements Comparable {
  private $number;

  public function __construct($number) {
    if (is_null($number) || !is_integer($number) || $number < 19000000) {
      throw new IllegalArgumentException($number, __FILE__, __LINE__);
    }
    $this->number = $number;
  }

  public function __toString() {
    return $this->toString();
  }

  public function toString() {
    return strval($this->number);
  }

  public function isEqual(Comparable $cn) {
    return $this->number == $cn->toNumber();
  }
  
  public function toNumber() {
    return $this->number;
  }

  public function display() {
	  return intval(substr($this->number, 4)) . "/" . substr($this->number, 0, 4);
  }

  public static function parse($number) {
    if (is_null($number) || strtolower($number) == 'null' || $number == '') {
      return null;
    }
    
    $value = null;
    if (strpos($number, "/") > 0) {
      $parts = explode("/", $number);
      if (strlen($parts[1]) >= 1 && strlen($parts[1]) <= 2) {
        $century = "19";
        if ($parts[1] < 70) {
          $century = "20";
        }
        $parts[1] = $century . sprintf("%02d", $parts[1]);
      }
      $value = intval($parts[1]) * 10000 + intval(sprintf("%04d", $parts[0]));
    }
    else if (is_numeric($number)) {
      $value = intval($number + 0); // This will trigger $number to be converted to an integer
    }
    else if (is_float($number)) {
      $value = intval($number);
    }
    else {
      $value = $number;
    }
    
    $result = null;
    try {
      $result = new CaseNumber($value);
    }
    catch (IllegalArgumentException $e) {
      // Ignore
    }
    return $result;
  }
}
  