<?php

namespace PhatCats\Test\Typeclass\Alternative;

use PhatCats\Attempt\AttemptAlternative;
use PhatCats\Attempt\Attempt;
use PhatCats\LinkedList\LinkedListAlternative;
use PhatCats\LinkedList\LinkedListFactory;

class AttemptAlternativeTest extends AlternativeTest {

  public function setUp() {
    parent::setUp();
  }

  protected function getAlternative() {
    return new AttemptAlternative(new LinkedListAlternative(new LinkedListFactory()));
  }

  protected function getOne() {
    return Attempt::fromValue(1);
  }

  protected function getTwo() {
    return Attempt::fromValue(2);
  }

  protected function getThree() {
    return Attempt::fromValue(3);
  }

  public function testOrFailureFailure() {
    $failure1 = Attempt::failure("hello");
    $failure2 = Attempt::failure("world");
    $ored = $this->alternative->or($failure1, $failure2);

    $this->assertEquals($failure2, $ored);
  }

  public function testOrSuccessSuccess() {
    $success1 = Attempt::fromValue("hello");
    $success2 = Attempt::fromValue(" world!");
    $ored = $this->alternative->or($success1, $success2);
    $expexted = $success1;

    $this->assertEquals($expexted, $ored);
  }
}
