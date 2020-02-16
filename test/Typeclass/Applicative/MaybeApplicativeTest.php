<?php

namespace PhatCats\Test\Typeclass\Applicative;

use PhatCats\Maybe\Maybe;
use PhatCats\Maybe\MaybeMonad;

class MaybeApplicativeTest extends ApplicativeTest {

  public function getApplicative() {
    return new MaybeMonad();
  }

  public function testTakeAndThen() {
    $m1 = Maybe::fromValue(1);
    $m2 = Maybe::fromValue(2);

    $result = $this->getApplicative()->takeAndThen($m1, $m2);
    $expected = $m1;

    $this->assertEquals($expected, $result);
  }

  public function testAndThenTake() {
    $m1 = Maybe::fromValue(1);
    $m2 = Maybe::fromValue(2);

    $result = $this->getApplicative()->andThenTake($m1, $m2);
    $expected = $m2;

    $this->assertEquals($expected, $result);
  }
}

