<?php
namespace sbronsted;

use DateInterval;
use DateTime;
use DateTimeZone;

/**
 * Class Date implement date operations and is geared towards mysql date column
 */
class Date implements Comparable {
  protected $date;
  const FMT_DA = "d-m-Y";
  const FMT_DA_LONG = "d-m-Y H:i:s";
  const FMT_MYSQL = "Y-m-d";
  const FMT_MYSQL_LONG = "Y-m-d H:i:s";
  const FMT_YMD = "Ymd";
  const TIMEZONE = "Europe/Copenhagen";

  public function __construct($date = null) {
    if (is_null($date)) {
      $this->date = new DateTime("now", new DateTimeZone(self::TIMEZONE));
      if (get_called_class() == Date::class) {
      	$this->date->setTime(0,0,0,0);
			}
    }
    else {
      if (is_object($date)) {
        if ($date instanceof Date) {
          $this->date = clone $date->date;
        }
        else if ($date instanceof DateTime) {
          $this->date = clone $date;
        }
      }
      else {
        throw new IllegalArgumentException("date", __FILE__, __LINE__);
      }
    }
  }

  protected function hasTime() {
    return false;
  }
  
  public function __toString() {
    return $this->toString();
  }
  
  public function toString() {
    return $this->format($this->hasTime() ? self::FMT_MYSQL_LONG : self::FMT_MYSQL);
  }

  public function format($fmt) {
    return ($this->date != null ? $this->date->format($fmt) : "");
  }

  public function __get($name) {
    // This needs to be expanded when the need arises
    $fmt = "";
    switch ($name) {
      case "year" :
        $fmt = "Y";
        break;
      case "month" :
        $fmt = "m";
        break;
      case "day" :
        $fmt = "d";
        break;
      case "date" :
        $fmt = "Ymd";
        break;
      case "time" :
        $fmt = "His";
        break;
      case "datetime" :
        $fmt = "YmdHis";
        break;
      default:
        throw new IllegalArgumentException($name, __FILE__, __LINE__);
    }
    return intval($this->format($fmt));    
  }

  public function __set($name, $value) {
    $day = $this->day;
    $year = $this->year;
    $month = $this->month;
    switch ($name) {
      case "day":
        $this->date->setDate($year, $month, intval($value));
        break;
      case "month":
        $this->date->setDate($year, intval($value), $day);
        break;
      case "year":
        $this->date->setDate(intval($value), $month, $day);
        break;
      default:
        throw new IllegalArgumentException($name, __FILE__, __LINE__);
    }
  }
  
  public function rollForward(DateInterval $interval) {
    $this->date->add($interval);
  }
  
  public function rollBackward(DateInterval $interval) {
    $this->date->sub($interval);
  }
  
  public function isEqual(Comparable $other) {
    $fmt = "YmdHis";
    return ($this->format($fmt) - $other->format($fmt)) == 0;
  }
  
  public function isAfter(Date $other) {
    $fmt = "YmdHis";
    return ($this->format($fmt) - $other->format($fmt)) > 0;
  }

  public function isBefore(Date $other) {
    $fmt = "YmdHis";
    return ($this->format($fmt) - $other->format($fmt)) < 0;
  }

  public function isBetween(Date $from, Date $to) {
    $fmt = "YmdHis";
    $date = $this->format($fmt);
    return ($date - $from->format($fmt)) >= 0 && ($date - $to->format($fmt)) <= 0;
  }

  public function diff(Date $other) {
    $days = $this->date->diff($other->date)->days;
    return ($this->isAfter($other) ? $days : -$days);
  }

	public function getTimestamp() {
  	return $this->date->getTimestamp();
	}

  public static function parse($date, $fmt = null) {
    if (empty($date) || strlen($date) < 4 || substr($date,0,4) == "0000" || strtolower($date) == 'null') {
      return null;
    }
    if (is_null($fmt)) { // Try and guess date format
      $fmt = self::FMT_DA;
      if (self::isMysqlDate($date)) {
        $fmt = self::FMT_MYSQL;
      }
      else if (strlen($date) == 8) {
      	$fmt = self::FMT_YMD;
      }
    }
    if (strlen($date) > strlen('9999-12-31')) {
    	$date = substr($date, 0, 10);
    }
    $dt = DateTime::createFromFormat($fmt, $date, new DateTimeZone(self::TIMEZONE));
    if ($dt === false) {
      throw new IllegalArgumentException("date: $date", __FILE__, __LINE__);
    }
    $dt->setTime(0,0,0);
    return new Date($dt);
  }
  
  protected static function isMysqlDate($date) {
    //Mysql date is on the form yyyy-mm-dd
    if ($date != "" && $date[4] == '-' && $date[7] == '-') {
      return true;
    }
    return false;
  }
}
