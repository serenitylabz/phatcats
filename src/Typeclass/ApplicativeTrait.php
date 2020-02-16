<?php

namespace PhatCats\Typeclass;

// function _const($x) {
//   return function($y) {
//     return $y;
//   };
// }

// function flippedConst($x) {
//   return function($y) use ($x) {
//     return $x;
//   };
// }

trait ApplicativeTrait {

  public function takeAndThen($first, $second) {
    $const = function($x, $y) {
      return $x;
    };
    $ff = $this->pure($const);

    return $this->apply($this->apply($ff, $first), $second);
  }

  public function andThenTake($first, $second) {
    $flippedConst = function($x, $y) {
      return $y;
    };
    $ff = $this->pure($flippedConst);

    return $this->apply($this->apply($ff, $first), $second);
  }
}
