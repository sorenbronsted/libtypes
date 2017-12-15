<?php
namespace ufds;

use ErrorException;

class CprException extends ErrorException {
  public function __construct($number, $file, $line) {
    parent::__construct("Cpr $number is not valid ($file,$line)");
  }
}
