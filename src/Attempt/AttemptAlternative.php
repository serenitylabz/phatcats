<?php

namespace PhatCats\Attempt;

use PhatCats\Typeclass\Alternative;
use PhatCats\Typeclass\BaseApplicativeForObjectApplicative;
use PhatCats\Attempt\Attempt;

class AttemptAlternative extends BaseApplicativeForObjectApplicative implements Alternative {

  private $errorAlternative;

  public function __construct(Alternative $errorAlternative) {
    $this->errorAlternative = $errorAlternative;
  }

  function or($left, $right) {
    return $left->isFailure() ? $right : $left;
  }

  function empty() {
    return Attempt::failure($this->errorAlternative->empty());
  }

  function pure($v) {
    return Attempt::fromValue($v);
  }
}
