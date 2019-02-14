<?php
namespace ufds;

use DateTime;
use DateTimeZone;

class Timestamp extends Date {

  public function __construct($datetime = null) {
    parent::__construct($datetime);
  }
  
  public function diff(Date $other) {
    return $this->date->getTimestamp() - $other->date->getTimestamp();
  }
  
  public function __get($name) {
    $fmt = "";
    switch ($name) {
      case "hour" :
        $fmt = "H";
        break;
      case "minute" :
        $fmt = "i";
        break;
      case "second" :
        $fmt = "s";
        break;
      default:
        return parent::__get($name);
    }
    return intval($this->format($fmt));    
  }

  public function __set($name, $value) {
    $hour = $this->hour;
    $minute = $this->minute;
    $second = $this->second;
    switch ($name) {
      case "hour":
        $this->date->setTime(intval($value), $minute, $second);
        break;
      case "minute":
        $this->date->setTime($hour, intval($value), $second);
        break;
      case "second":
        $this->date->setTime($hour, $minute, intval($value));
        break;
      default:
        parent::__set($name, $value);
    }
  }

  public function getDate() {
  	return new Date($this->date);
  }

	public static function parse($timestamp, $fmt = null) {
		if (empty($timestamp) || strlen($timestamp) < 4 || substr($timestamp,0,4) == "0000" || strtolower($timestamp) == 'null') {
			return null;
		}
		if (is_null($fmt)) { // Try and guess date format
			$fmt = self::FMT_DA_LONG;
			if (self::isMysqlDate($timestamp)) {
				$fmt = self::FMT_MYSQL_LONG;
			}
			else if (strlen($timestamp) > 10) {
				$fmt = self::FMT_DA_LONG;
			}
		}
		if (strlen($timestamp) <= strlen('9999-12-31')) {
			$timestamp .= ' 00:00:00';
		}
		if (strlen($timestamp) > strlen('9999-12-31 00:00:00')) {
			$timestamp = substr($timestamp,0,19);
		}
		$dt = DateTime::createFromFormat($fmt, $timestamp, new DateTimeZone(self::TIMEZONE));
		if ($dt === false) {
			throw new IllegalArgumentException("Timestamp: $timestamp", __FILE__, __LINE__);
		}
		return new Timestamp($dt);
	}


	protected function hasTime() {
    return true;
  }
}
