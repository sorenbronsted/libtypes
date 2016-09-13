<?php
namespace ufds;

use PHPUnit_Framework_TestCase;

require_once 'test/settings.php';

class TimestampTest extends PHPUnit_Framework_TestCase {

  public function testDefault() {
    $s = "2012-03-01 12:30:45";
    $t = Timestamp::parse($s);
    $this->assertNotNull($t);
    $this->assertEquals($s, $t->toString());
  }

  public function testAsDate() {
    $s = "2012-03-01";
    $t = Timestamp::parse($s);
    $this->assertNotNull($t);
    $this->assertEquals($s.' 00:00:00', $t->toString());
  }

	public function testdiff() {
		$ts1 = Timestamp::parse('01-01-2014 00:00:00');
		$ts2 = Timestamp::parse('01-01-2014 00:00:10');
		$this->assertEquals(10, $ts2->diff($ts1));
		$ts2 = Timestamp::parse('01-01-2014 00:10:00');
		$this->assertEquals(10 * 60, $ts2->diff($ts1));
		$ts2 = Timestamp::parse('01-01-2014 02:00:00');
		$this->assertEquals(2 * 60 * 60, $ts2->diff($ts1));
		$ts2 = Timestamp::parse('02-01-2014 00:00:00');
		$this->assertEquals(24 * 60 * 60, $ts2->diff($ts1));
	}
	
	public function testCompare() {
		$ts1 = Timestamp::parse('01-01-2015 00:00:01');
		
		$this->assertTrue('2015-01-01 00:00:01' == $ts1);
		$this->assertTrue('2015-01-01 00:00:02' != $ts1);
		
		$this->assertTrue($ts1 === $ts1);

		$ts2 = Timestamp::parse('01-01-2015 00:00:01');
		$this->assertTrue($ts1 == $ts2);

		$ts2 = Timestamp::parse('01-01-2015 00:00:02');
		$this->assertTrue($ts1 != $ts2);
	}
}
