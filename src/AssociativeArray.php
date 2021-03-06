<?php

namespace PhatCats;

use PhatCats\ObjectTypeclass\ObjectTraversable;

class UnsupportedOperationException extends \Error {}

/**
 * AssociativeArray maps keys to values.  Keys and values can be any type of
 * data. AssociativeArray's are immutable.  This means that some operations will
 * throw an exception.  For example the mutating methods of the ArrayAccess
 * interface throw UnsupportedOperationException.
 */
class AssociativeArray implements \ArrayAccess, \IteratorAggregate {
  use ObjectTraversable;

  protected $array;

  public function __construct($array) {
    $this->array = is_null($array) ? [] : $array;
  }

  public function traverse(callable $f, $monad) {

	// Initial value for the fold: an empty array wrapped in a default
	// context.
	$init = $monad->pure([]);

	// Define the folding function.
	$foldingFn = function ($acc, $curr) use ($f, $monad) {

      // Call $f on the current value of the array, $curr. The return
      // value should be a monadic value.
      try {
        $returnedMonad = $f($curr);

        // If the result is null, we fail.
        if (is_null($returnedMonad)) {
          $returnedMonad = $monad->fail('The callable passed to `AssociativeArray::traverse` returned null.');
        }
      } catch (\Exception $e) {
        $returnedMonad = $monad->fail('The callable passed to `AssociativeArray::traverse` threw an exception: ' . $e->getMessage());
      }

      // Put the value wrapped by the above monadic value in the array
      // held by the accumulator, $acc, to get the new accumulator.
      $newAcc = $returnedMonad->flatMap(function ($newVal) use ($acc) {
		return $acc->map(function ($arr) use ($newVal) {
          $arr[] = $newVal;
          return $arr;
		});
      });

      return $newAcc;
	};

	// Do the fold.
    // The type here is Monad[array]
	$result = array_reduce($this->array, $foldingFn, $init);

    // convert the type to Monad[AssociativeArray[a]]
    $result = $monad->map($result, function ($arr) { return new AssociativeArray($arr); });

	return $result;
  }

  public function offsetExists($offset): bool {
    return array_key_exists($offset, $this->array);
  }

  public function offsetGet($offset) {
    return $this->array[$offset];
  }

  public function offsetSet($offset, $value) {
    throw new UnsupportedOperationException();
  }

  public function offsetUnset($offset) {
    throw new UnsupportedOperationException();
  }

  public function getIterator() : \Traversable {
    return new \ArrayIterator($this->array);
  }
}
