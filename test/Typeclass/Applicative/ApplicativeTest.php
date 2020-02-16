<?php

namespace PhatCats\Test\Typeclass\Applicative;

use PHPUnit\Framework\TestCase;
use PhatCats\Maybe\Maybe;
use PhatCats\Maybe\MaybeT;
use PhatCats\Either\Either;
use PhatCats\Validation\Validation;

abstract class ApplicativeTest extends TestCase {

  private $applicative;

  abstract function getApplicative();

  public function testIdentity() {
    $applicative = $this->getApplicative();
    $ff = $applicative->pure('identity');
    $fa = $applicative->pure("hello");
    $result = $applicative->apply($ff, $fa);

    $this->assertEquals($fa, $result);
  }

  public function testComposition() {
    $applicative = $this->getApplicative();
    $compose = $applicative->pure('compose');
    $ff = $applicative->pure('addOne');
    $fg = $applicative->pure('addOne');
    $fa = $applicative->pure(1);
    $result = $applicative->apply(
      $applicative->apply(
        $applicative->apply($compose, $ff),
        $fg
      ),
      $fa
    );
    $expectedResult  = $applicative->pure(3);

    $this->assertEquals($expectedResult, $result);
  }

  public function testHomomorphism() {
    $applicative = $this->getApplicative();
    $ff = $applicative->pure('addOne');
    $fa = $applicative->pure(1);
    $result = $applicative->apply($ff, $fa);
    $exptectedResult = $applicative->pure(addOne(1));

    $this->assertEquals($exptectedResult, $result);
  }

  public function testTakeAndThen() {
    $m1 = $this->getApplicative()->pure(1);
    $m2 = $this->getApplicative()->pure(2);

    $result = $this->getApplicative()->takeAndThen($m1, $m2);
    $expected = $m1;

    $this->assertEquals($expected, $result);
  }

  public function testAndThenTake() {
    $m1 = $this->getApplicative()->pure(1);
    $m2 = $this->getApplicative()->pure(2);

    $result = $this->getApplicative()->andThenTake($m1, $m2);
    $expected = $m2;

    $this->assertEquals($expected, $result);
  }
}
