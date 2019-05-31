<?php

namespace PhatCats\Typeclass;

use PhatCats\LinkedList\LinkedListFactory;

trait AlternativeTrait {

  /**
   * Repeatedly applies the action to return zero or more values.
   *
   * @return :: m (LinkedList a)
   */
  function zeroOrMore($v) {
    $nil = $this->factory->empty();

    return $this->or($this->oneOrMore($v), $nil);
  }

  /**
   * Repeatedly applies the action to return one or more values.
   *
   * @return :: m (LinkedList a)
   */
  function oneOrMore($v) {
    // define a function for consing an element to a list
    $cons = function($e, $l) {
      return $l->cons($x);
    };

    $ff = $this->pure($cons);
    $ffv = $this->apply($ff, $v);

    return $this->apply($ffv, $this->zeroOrMore());
  }
}
