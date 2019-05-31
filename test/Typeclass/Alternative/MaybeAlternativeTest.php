<?php

namespace PhatCats\Test\Typeclass\Alternative;

use PhatCats\Maybe\MaybeAlternative;
use PhatCats\Maybe\Maybe;

class MaybeAlternativeTest extends AlternativeTest {

  public function setUp() {
    parent::setUp();
  }

  protected function getAlternative() {
    return new MaybeAlternative();
  }

  protected function getOne() {
    return Maybe::fromValue(1);
  }

  protected function getTwo() {
    return Maybe::fromValue(2);
  }

  protected function getThree() {
    return Maybe::fromValue(3);
  }

  public function testOrNothingNothing() {
    $nothing1 = Maybe::nothing();
    $nothing2 = Maybe::nothing();
    $ored = $this->alternative->or($nothing1, $nothing2);

    $this->assertEquals($nothing1, $ored);
  }

  public function testOrJustJust() {
    $just1 = Maybe::fromValue("hello");
    $just2 = Maybe::fromValue(" world!");
    $ored = $this->alternative->or($just1, $just2);
    $expexted = $just1;

    $this->assertEquals($expexted, $ored);
  }
}
