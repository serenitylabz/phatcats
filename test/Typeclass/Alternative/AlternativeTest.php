<?php

namespace PhatCats\Test\Typeclass\Alternative;

use PHPUnit\Framework\TestCase;

abstract class AlternativeTest extends TestCase {

  protected $alternative;
  protected $alt1, $alt2, $alt3;

  protected abstract function getAlternative();
  protected abstract function getOne();
  protected abstract function getTwo();
  protected abstract function getThree();

  public function setUp() {
    $this->alternative = $this->getAlternative();
    $this->alt1 = $this->getOne();
    $this->alt2 = $this->getTwo();
    $this->alt3 = $this->getThree();
  }

  public function testAssociativity() {

    $alt1alt2 = $this->alternative->or($this->alt1, $this->alt2);
    $alt2alt3 = $this->alternative->or($this->alt2, $this->alt3);

    $first = $this->alternative->or($alt1alt2, $this->alt3);
    $second = $this->alternative->or($this->alt1, $alt2alt3);

    $this->assertAlternativesEqual($first, $second);
  }

  public function testLeftEmpty() {
    $ident = $this->alternative->empty();
    $result = $this->alternative->or($ident, $this->alt1);

    $this->assertAlternativesEqual($this->alt1, $result);
  }

  public function testRightEmpty() {
    $ident = $this->alternative->empty();
    $result = $this->alternative->or($this->alt1, $ident);

    $this->assertAlternativesEqual($this->alt1, $result);
  }

  protected function assertAlternativesEqual($alt1, $alt2) {
    return $this->assertEquals($alt1, $alt2);
  }
}
