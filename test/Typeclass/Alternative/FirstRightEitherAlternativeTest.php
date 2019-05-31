<?php

namespace PhatCats\Test\Typeclass\Alternative;

use PhatCats\Either\Alternative\FirstRightEitherAlternative;
use PhatCats\Either\Either;
use PhatCats\LinkedList\LinkedListAlternative;
use PhatCats\LinkedList\LinkedListFactory;

class FirstRightEitherAlternativeTest extends AlternativeTest {

  public function setUp() {
    parent::setUp();
  }

  protected function getAlternative() {
    return new FirstRightEitherAlternative(new LinkedListAlternative(new LinkedListFactory()));
  }

  protected function getOne() {
    return Either::fromValue(1);
  }

  protected function getTwo() {
    return Either::fromValue(2);
  }

  protected function getThree() {
    return Either::fromValue(3);
  }

  public function testOrLeftLeft() {
    $left1 = Either::left("hello");
    $left2 = Either::left("world");
    $ored = $this->alternative->or($left1, $left2);

    $this->assertEquals($left2, $ored);
  }

  public function testOrRightRight() {
    $right1 = Either::fromValue("hello");
    $right2 = Either::fromValue(" world!");
    $ored = $this->alternative->or($right1, $right2);
    $expexted = $right1;

    $this->assertEquals($expexted, $ored);
  }
}
