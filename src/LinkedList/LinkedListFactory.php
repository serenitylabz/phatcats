<?php

namespace TMciver\Functional\LinkedList;

class LinkedListFactory {

  private static $empty;

  public function __construct() {
    // Ideally, this will be the only Nil object in the application;
    self::$empty = is_null(self::$empty) ? new Nil() : self::$empty;
  }

  public function pure($val) {
    // put a value into a singleton list.
    return new Cons($val, self::$empty);
  }

  public function empty() {
    return self::$empty;
  }

  /**
   * Creates a `LinkedList` from a native PHP array.
   */
  public function fromNativeArray(array $array) {
    if (empty($array)) {
      return self::$empty;
    } else {
      return new ArrayBackedLinkedList($array);
    }
  }

  public function range($start, $end, $step = 1) {
    return $this->fromNativeArray(range($step, $end, $step));
  }

  /**
   * @param $l The LinkedList to be cycled.
   * @return An infinite LinkedList that cycles the elements of the input
   *         LinkedList.
   */
  public function cycle($l) {

    if ($l->size() == 0) {
      $cycled = $l;
    } else {
      // make a copy of $l that is all Cons's
      //$id = function ($x) { return $x; };
      $cycled = $l->foldRight($this->empty(), function ($x, $l) {
	  return $l->cons($x);
	});

      // Get a ref to the last element.
      $last = $cycled->foldLeft($cycled, function ($node, $x) {
	  return $node->tail()->isEmpty() ?
	    $node :
	    $node->tail();
	});

      // Use reflection to mutate the tail reference of the last element.
      $tailProp = new \ReflectionProperty('\TMciver\Functional\LinkedList\Cons', 'tail');
      $tailProp->setAccessible(true);
      $tailProp->setValue($last, $cycled);
    }

    return $cycled;
  }
}
