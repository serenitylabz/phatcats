<?php

namespace PhatCats\LinkedList;

class LinkedListFactory {

  private static $empty;

  public function __construct() {
    // Ideally, this will be the only Nil object in the application;
    self::$empty = is_null(self::$empty) ? new Nil($this) : self::$empty;
  }

  public function pure($val) {
    // put a value into a singleton list.
    return new Cons($val, self::$empty, $this);
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
      return new ArrayBackedLinkedList($array, $this);
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
      $tailProp = new \ReflectionProperty('\PhatCats\LinkedList\Cons', 'tail');
      $tailProp->setAccessible(true);
      $tailProp->setValue($last, $cycled);
    }

    return $cycled;
  }

  /**
   * @param $v The value to repeat.
   * @return An infinite Linkedlist all of whose elements is $v.
   */
  public function repeat($v) {
    return $this->cycle(new Cons($v, self::$empty, $this));
  }

  /**
   * @param $n :: int. The number of elements in the returned `Linkedlist`.
   * @param $v :: a. The value to replicate.
   * @return A Linkedlist of length $n each of whose elements are $v.
   */
  public function replicate($n, $v) {
    return $this->repeat($v)->take($n);
  }

  /**
   * @param $f :: b -> Maybe (a, b). The function takes the element and returns
   *        Nothing if it is done producing the list or returns Just (a,b), in
   *        which case, a is a prepended to the list and b is used as the next
   *        element in a recursive call.
   * @param $init :: b. The seed value.
   * @return :: Linkedlist[a]. The generated list.
   */
  public function unfold($f, $init) {
    $l = $this->empty();
    $result = $f($init);
    while (!$result->isNothing()) {
      $tuple = $result->get();
      $nextVal = $tuple->first();
      $nextB = $tuple->second();
      $l = $l->cons($nextVal);
      $result = $f($nextB);
    }

    return $l->reverse();
  }
}
