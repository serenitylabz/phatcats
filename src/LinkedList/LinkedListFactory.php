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
    if (is_null($array) || empty($array)) {
      return self::$empty;
    } else {
      $fn = function ($list, $item) {
	return $list->cons($item);
      };
      return array_reduce(array_reverse($array), $fn, self::$empty);
    }
  }
}
