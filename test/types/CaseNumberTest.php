<?php
namespace sbronsted;

use PHPUnit\Framework\TestCase;

require_once 'test/settings.php';

class CaseNumberTest extends TestCase {
  private $values  = array(20110010, "20110011", 2.0110012e+07, "2.0110013e+07", "14/2011", "15/11",  "1/1",   "2/2002");
  private $answers = array(20110010, 20110011,   20110012,      20110013,         20110014, 20110015, 20010001, 20020002);
  
  public function testParse() {
    foreach($this->values as $idx => $src) {
      $cn = CaseNumber::parse($src);
      $this->assertNotNull($cn, $src);
      $this->assertEquals($this->answers[$idx], $cn->toNumber());
    }
    
    // failure cases
    $this->assertNull(CaseNumber::parse("a"));
    $this->assertNull(CaseNumber::parse("1"));
    $this->assertNull(CaseNumber::parse("1.2"));
		$this->assertNull(CaseNumber::parse(''));
    $this->assertNull(CaseNumber::parse(null));
  }
  
  public function testIsEquals() {
    foreach($this->values as $idx => $src) {    
      $srcCn = CaseNumber::parse($src);
      $this->assertNotNull($srcCn);
      $tstCn = CaseNumber::parse($this->answers[$idx]);
      $this->assertNotNull($tstCn);
      $this->assertTrue($tstCn->isEqual($srcCn));
    }
    
    // failure cases
    $srcCn = CaseNumber::parse("10/10");
    $this->assertNotNull($srcCn);
    $tstCn = CaseNumber::parse("11/11");
    $this->assertNotNull($tstCn);
    $this->assertFalse($tstCn->isEqual($srcCn));
  }
  
  public function testToString() {
    $cn = new CaseNumber(20110001);
    $this->assertTrue(is_string($cn->__toString()));
    $this->assertEquals("20110001", $cn);
  }
  
  public function testIllegalArgument() {
    $values = array(null, "abc", 10);
    foreach($values as $value) {
      try {
        new CaseNumber($value);
        $this->fail("Exception expected");
      }
      catch (IllegalArgumentException $e) {
        $this->assertEquals(IllegalArgumentException::class, get_class($e));
      }
    }
  }
	
	public function testCompare() {
		$cn1 = CaseNumber::parse(20150101);
		$this->assertTrue('20150101' == $cn1);
		$this->assertTrue('20150102' != $cn1);
		
		$cn2 = CaseNumber::parse(20150101);
		$this->assertTrue($cn2 == $cn1);

		$cn2 = CaseNumber::parse(20150102);
		$this->assertTrue($cn2 != $cn1);
	}

	public function testDisplay() {
		$cn = CaseNumber::parse(20150101);
		$this->assertEquals('101/2015', $cn->display());
		$cn = CaseNumber::parse(20151001);
		$this->assertEquals('1001/2015', $cn->display());
	}
}
