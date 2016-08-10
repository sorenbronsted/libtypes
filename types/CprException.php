<?php
namespace ufds;

use ErrorException;

class CprException extends ErrorException {
  public function __construct($file, $line) {
    parent::__construct("Cpr is not valid ($file,$line)");
  }
}
