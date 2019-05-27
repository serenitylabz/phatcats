<?php

namespace PhatCats\LinkedList;

use PhatCats\Maybe\Maybe;
use PhatCats\Tuple;
use PhatCats\Typeclass\SemiGroup;

/**
 * This class implements the `LinkedList` API using a native PHP array.
 * Note that clients should not use this class directly.
 */
class ArrayBackedLinkedList extends LinkedList {

  private $array;
  private $size;

  /**
   * This class should only be constructed by the class itself or
   * `LinkedListFactory`.
   */
  public function __construct(array $array, LinkedListFactory $factory) {
    parent::__construct($factory);
    $this->array = array_values($array);
    $this->size = count($array);
  }

  public function head() {
    if (count($this->array) == 0) {
      return Maybe::nothing();
    } else {
      return Maybe::fromValue($this->array[0]);
    }
  }

  public function tail() {
    if ($this->size <= 1) {
      return $this->factory->empty();
    } else {
      return new ArrayBackedLinkedList(array_slice($this->array, 1), $this->factory);
    }
  }

  public function take($n) {
    return ($this->isEmpty()) ?
      $this->factory->empty() :
      new ArrayBackedLinkedList(array_slice($this->array, 0, $n), $this->factory);
  }

  public function drop($n) {
    return $n < 1 ?
      $this :
      new ArrayBackedLinkedList(array_slice($this->array, $n), $this->factory);
  }

  public function isEmpty() {
    return empty($this->array);
  }

  protected function toNativeArrayPrivate(array &$array, $idx) {
    array_splice($array, $idx, $this->size, $this->array);
    return $array;
  }

  public function remove($value) {
    // get the index of the first occurrence of $value
    $first = array_search($value, $this->array);

    if ($first !== FALSE) {
      // remove the found element using array_splice as this will re-index the
      // numeric keys.

      // First, copy the array
      $a = $this->array;
      array_splice($a, $first, 1);

      return new ArrayBackedLinkedList($a, $this->factory);
    } else {
      // $value was not found; just return $this
      return $this;
    }
  }

  public function contains($value) {
    return in_array($value, $this->array);
  }

  public function size() {
    return $this->size;
  }

  public function foldLeft($init, callable $f) {
    return array_reduce($this->array, $f, $init);
  }

  public function foldRight($init, callable $f) {
    $reversed = array_reverse($this->array);

    // need to create a new folding function that swaps the two arguments from
    // that in the given function.
    $g = function ($x, $acc) use ($f) { return $f($acc, $x); };

    return array_reduce($reversed, $g, $init);
  }

  public function filter($pred) {
    $filtered = array_filter($this->array, $pred);
    return new ArrayBackedLinkedList($filtered, $this->factory);
  }

  public function append($list, SemiGroup $semiGroup = null) {
    $merged = array_merge($this->array, $list->toNativeArray());
    return new ArrayBackedLinkedList($merged, $this->factory);
  }

  public function map(callable $f) {
    $resultArray = array_map($f, $this->array);

    return new ArrayBackedLinkedList($resultArray, $this->factory);
  }

  public function flatMap(callable $f) {
    $g = function ($acc, $x) use ($f) {
      try {
	$newList = $f($x);
	$nextAcc = $acc->concat($newList);
      } catch (\Exception $e) {
	// TODO: this does not currently cause the entire `flatMap` call to fail;
	// Will need to add state to the accumulator to track failure.
	$nextAcc = $this->fail('Got an exception from the callable passed to `LinkedList::flatMap`.');
      }

      return $nextAcc;
    };

    // TODO: hack until we have better equality check for LinkedLists
    $l = array_reduce($this->array, $g, $this->factory->empty());
    $ll = new ArrayBackedLinkedList($l->toNativeArray(), $this->factory);

    return $ll;
  }

  public function applyNoArg() {
    return $this->map(function ($f) {
	// TODO: what if $f throws?
	return $f();
      });
  }

  protected function applyToArg($argList) {
    $g = function ($acc, $f) use ($argList) {
      $nextArray = array_map($f, $argList->toNativeArray());
      return array_merge($acc, $nextArray);
    };
    $resultArray = array_reduce($this->array, $g, []);

    return new ArrayBackedLinkedList($resultArray, $this->factory);
  }

  protected function numConsCells() {
    return 0;
  }

  /**
   * @param $i :: int The index of the desired `LinkedList` element.
   * @return :: bool; True if the given index is in the range of this
   *         `LinkedList`; false otherwise.
   */
  protected function isInRange(int $i): bool {
    return $i >= 0 && $i < $this->size;
  }

  /**
   * @param $i :: int The index of the desired `LinkedList` element.
   * @return :: Maybe[a] The desired `LinkedList` element wrapped in a `Maybe`.
   */
  public function nth(int $i): Maybe {
    return $this->isInRange($i) ?
      Maybe::fromValue($this->array[$i]) :
      Maybe::nothing();
  }

  final public function takeWhile(callable $f): LinkedList {
    $arr = [];
    for ($i = 0; $i < $this->size; $i++) {
      if ($f($this->array[$i])) {
        $arr[] = $this->array[$i];
      } else {
        break;
      }
    }

    return new ArrayBackedLinkedList($arr, $this->factory);
  }

  final public function dropWhile(callable $f): LinkedList {
    // Find the first element that does not satisfy the predicate.
    $i = 0;
    foreach ($this->array as $key => $value) {
        if (!$f($value)) {
          $i = $key;
        }
    }

    // Slice the array from that point to the end.
    $arr = array_slice($this->array, $i);

    return new ArrayBackedLinkedList($arr, $this->factory);
  }

  final public function splitAt(int $i) {
    if ($i < 1) {
      $leftLinkedList = new ArrayBackedLinkedList([], $this->factory);
      $rightLinkedList = $this;
    } else {
      $leftLinkedList = new ArrayBackedLinkedList(array_slice($this->array, 0, $i), $this->factory);
      $rightLinkedList = new ArrayBackedLinkedList(array_slice($this->array, $i), $this->factory);
    }
    $split = new Tuple($leftLinkedList, $rightLinkedList);

    return $split;
  }

  /**
   * @see PhatCats\LinkedList\LinkedList::all()
   */
  public function all(callable $pred): bool {
    $result = true;
    foreach ($this->array as $value) {
      $result = $result && $pred($value);
      if (!$result) {
        break;
      }
    }

    return $result;
  }

  /**
   * @see PhatCats\LinkedList\LinkedList::any()
   */
  public function any(callable $pred): bool {
    $result = false;
    foreach ($this->array as $value) {
      $result = $result || $pred($value);
      if ($result) {
        break;
      }
    }

    return $result;
  }
}
