<?php

namespace PhatCats\Test\Typeclass\Alternative;

use PhatCats\LinkedList\LinkedListAlternative;
use PhatCats\LinkedList\LinkedListFactory;
//use PhatCats\LinkedList\LinkedListFactory;

class LinkedListAlternativeTest extends AlternativeTest {

  private $factory;

  public function setUp() {
    $this->factory = new LinkedListFactory();
    parent::setUp();
  }

  protected function getAlternative() {
    return new LinkedListAlternative($this->factory);
  }

  protected function getOne() {
    return $this->factory->pure(1);
  }

  protected function getTwo() {
    return $this->factory->pure(2);
  }

  protected function getThree() {
    return $this->factory->pure(3);
  }

  public function testOrEmptyEmpty() {
    $empty = $this->factory->empty();
    $ored = $this->alternative->or($empty, $empty);

    $this->assertEquals($empty, $ored);
  }

  public function testOrListList() {
    $just1 = $this->factory->fromNativeArray([1, 2, 3]);
    $just2 = $this->factory->fromNativeArray([4, 5, 6]);
    $ored = $this->alternative->or($just1, $just2);
    $expexted = $this->factory->fromNativeArray([1, 2, 3, 4, 5, 6]);

    $this->assertEquals($expexted, $ored);
  }
}
