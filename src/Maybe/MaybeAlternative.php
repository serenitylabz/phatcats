<?php

namespace PhatCats\Maybe;

use PhatCats\Typeclass\Alternative;
use PhatCats\Typeclass\BaseApplicativeForObjectApplicative;
use PhatCats\Maybe\Maybe;

class MaybeAlternative extends BaseApplicativeForObjectApplicative implements Alternative {

  function or($left, $right) {
    return $left->isNothing() ? $right : $left;
  }

  function empty() {
    return Maybe::nothing();
  }

  function pure($v) {
    return Maybe::fromValue($v);
  }
}
