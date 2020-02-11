<?php

namespace PhatCats\LinkedList;

use PhatCats\Typeclass\Alternative;
use PhatCats\Typeclass\BaseApplicativeForObjectApplicative;
use PhatCats\LinkedList\LinkedList;

class LinkedListAlternative extends BaseApplicativeForObjectApplicative implements Alternative {

  private $factory;

  public function __construct(LinkedListFactory $factory) {
    $this->factory = $factory;
  }

  function or($left, $right) {
    return $left->concat($right);
  }

  function empty() {
    return $this->factory->empty();
  }

  function pure($v) {
    return $this->factory->pure($v);
  }
}
