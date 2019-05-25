<?php

namespace PhatCats\Test\Typeclass\Monad;

use PhatCats\Maybe\MaybeMonad;
use PhatCats\Maybe\Maybe;

/**
 * Class for testing `MaybeMonad`.
 * @see PhatCats\Test\Typeclass\Monad\MonadTest;
 */
class MaybeMonadTest extends MonadTest {

  private $maybeMonad;

  public function setUp() {
    $this->maybeMonad = new MaybeMonad();
  }

  protected function getMonad() {
    return $this->maybeMonad;
  }

  protected function getValue() {
    return 1;
  }

  protected function getMonadicFunctionF() {
    return function($i) { return $this->maybeMonad->pure($i + 1); };
  }

  protected function getMonadicFunctionG() {
    return function($i) { return $this->maybeMonad->pure($i . ''); };
  }

  public function testThen() {
    $just1 = Maybe::fromValue(1);
    $just2 = Maybe::fromValue(2);
    $result = $this->maybeMonad->then($just1, $just2);
    $expected = $just2;

    $this->assertEquals($expected, $result);
  }
}
