<?php
namespace ufds;

use PHPUnit\Framework\TestCase;

require_once 'test/settings.php';

class CprTest extends TestCase {

  public function testValid() {
    $cprs = array(
      "0103251234" => 19,
      "0103502234" => 19,
      "0103753234" => 19,
      "0103804234" => 19,
      "0103909234" => 19,
      "010390AAA1" => 19,
      "0103900000" => 19,
      "0103094234" => 20,
      "0103258234" => 20,
      "0103605234" => 18,
      "0103706234" => 18,
      "0103807234" => 18,
      "0103908234" => 18,
    );
    foreach ($cprs as $cpr_number => $test) {
      try {
        $cpr = new Cpr($cpr_number);
        $century = $cpr->getCentury();
        $this->assertEquals($test, $century, $cpr_number);
      }
      catch (CprException $e) {
        $this->fail("Unexpected exception");
      }
    }
  }

  public function testSex() {
    $female = new Cpr("0103251224");
    $this->assertEquals(false, $female->isMale());
    $male = new Cpr("0103251235");
    $this->assertEquals(true, $male->isMale());
  }
  
  public function testParse() {
    foreach(array("", null) as $value) {
      $value = Cpr::parse($value);
      $this->assertEquals(null, $value);
    }

    // Test undersized
    $cpr = Cpr::parse('101012222');
	  $this->assertEquals('0101012222', $cpr);

	  // with hypen
	  $cpr = Cpr::parse('10101-2222');
	  $this->assertEquals('0101012222', $cpr);

	  // only the first 10
	  $cpr = Cpr::parse('010101222233');
	  $this->assertEquals('0101012222', $cpr);
  }

  public function testGetDate() {
    $male = new Cpr("0103251235");
    $date = $male->getDate();
    $this->assertInstanceOf("ufds\Date", $date);
    $this->assertEquals(Date::parse("1925-03-01"), $date);
  }
  
  public function testGetAgeAt() {
    $male = new Cpr("0103251235");
    $age = $male->getAgeAt("20-01-1952");
    $this->assertEquals(1952-1925, $age);
    $age = $male->getAgeAt("1952-01-20");
    $this->assertEquals(1952-1925, $age);
		
    $male = new Cpr("0607060000"); // Foreign born after 2000
    $age = $male->getAgeAt("2010-01-20");
    $this->assertEquals(2010-2006, $age);
  }
	
	public function testCompare() {
		$cprStr = "0103251235";
    $cpr = new Cpr($cprStr);
		$this->assertEquals($cprStr, $cpr);
		$this->assertEquals(new Cpr($cprStr), $cpr);
		$this->assertTrue($cpr == $cprStr);

    $cpr = new Cpr('0101012222');
		$this->assertNotEquals($cprStr, $cpr);
		$this->assertNotEquals(new Cpr($cprStr), $cpr);
		$this->assertTrue($cpr != $cprStr);
	}

	public function testDisplay() {
		$cpr = Cpr::parse('101012222');
		$this->assertEquals('101012222', $cpr);
		$this->assertEquals('010101-2222', $cpr->display());

		$cpr = Cpr::parse('0101012222');
		$this->assertEquals('0101012222', $cpr);
		$this->assertEquals('010101-2222', $cpr->display());
	}

	public function testForeigner() {
		$cpr = Cpr::parse('0101012222');
		$this->assertEquals(false, $cpr->isForeigner());
		$cpr = Cpr::parse('010101AAA1');
		$this->assertEquals(true, $cpr->isForeigner());
		$cpr = Cpr::parse('010101ABC1');
		$this->assertEquals(true, $cpr->isForeigner());
		$cpr = Cpr::parse('0101010000');
		$this->assertEquals(true, $cpr->isForeigner());
		$cpr = Cpr::parse('0101010AB1');
		$this->assertEquals(true, $cpr->isForeigner());
	}

	public function testIsEqual() {
		$cpr1 = Cpr::parse('0101012222');
		$cpr2 = Cpr::parse('0101012222');
		$this->assertEquals(true, $cpr1->isEqual($cpr2));
		$this->assertEquals(true, $cpr2->isEqual($cpr1));

		$cpr2 = Cpr::parse('0101012223');
		$this->assertEquals(false, $cpr1->isEqual($cpr2));
		$this->assertEquals(false, $cpr2->isEqual($cpr1));
	}

	public function testNotParsable() {
  	$illegals = [
  		'0',
		  '-1',
		  '000000-0000',
		  'abcdefghij',
		  'aaaaaaaaaaaaaaaaaaaaaa',
		  'abcdef0101',
		  'na7183b4nm',
		  '012345.6e7',
		  '012345,6e7',
		  '.123456e-8',
	  ];
  	foreach ($illegals as $illegal) {
  		$this->assertNull(Cpr::parse($illegal));
	  }
	}
}
