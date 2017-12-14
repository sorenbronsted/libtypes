<?php
namespace ufds;

class Cpr implements Comparable {
  private $number;
  
  public function __construct($number) {
    if (strlen($number) != 10) {
      throw new IllegalArgumentException($number, __FILE__, __LINE__);
    }
    //The first 6 chars must be a date
	  if (Date::parse(substr($number, 0, 6), 'dmy') == null) {
		  throw new IllegalArgumentException($number, __FILE__, __LINE__);
	  }
	  $this->number = $number;
  }
  
  public function getDate() {
    $century = $this->getCentury();
    return Date::parse(substr($this->number,0,4).$century.substr($this->number,4,2), "dmY");
  }
  
  public function getAgeAt($date) {
    if (is_string($date)) {
      $date = Date::parse($date);
    }
    $birthDate = $this->getDate();
    return $date->year - $birthDate->year;
  }
  
  public function isMale() {
    return intval(substr($this->number,9,1)) % 2 == 1;
  }

  public function isForeigner() {
	  return !is_numeric(substr($this->number, 6)) || substr($this->number, 6) == '0000';
  }

  public function getCentury() {
    $result = "";
    if ($this->isForeigner()) {
			// UFDS foreigners can not be more than 100 years old, otherwise this will return incorrect century
			$year = substr($this->number, 4, 2);
			$currentYear = strftime('%g');
      $result = ($year <= $currentYear ? "20" : "19");
    }
    else { // Danish CPR rules
	    $sCentury = substr($this->number, 6, 1);
      $century = intval($sCentury);
      if ($century < 4) {
        $result = "19";
      }
      else {
        $year = intval(substr($this->number, 4, 2));
        if ($year >= 0 && $year <= 36) {
          $result = "20";
        }
        if ($year >= 37 && $year <= 99 && ($century == 4 || $century == 9)) {
          $result = "19";
        }
        if ($year >= 58 && $year <= 99 && !($century == 4 || $century == 9)) {
          $result = "18";
        }
      }
    }
    if (strlen($result) == 0) {
      throw new CprException(__FILE__, __LINE__);
    }
    return $result;
  }
  
  public function __toString() {
    return "".$this->number;
  }

  public function display() {
	  $s = str_pad($this->number,10,'0',STR_PAD_LEFT);
	  return substr($s,0,6).'-'.substr($s,6,4);
  }

  public static function parse($input) {
    if (is_null($input) || strlen(trim($input)) == 0) {
      return null;
    }
    $input = str_replace('-','',trim($input));
		$input = substr($input,0,10);
		if (strlen($input) == 9) {
			$input = '0'.$input;
		}
		$retval = null;
		try {
			$retval = new Cpr($input);
		}
		catch (IllegalArgumentException $e) {
			// do nothing
		}
    return $retval;
  }

  public function isEqual(Comparable $other) {
    return $this->number == $other->number;
  }
}