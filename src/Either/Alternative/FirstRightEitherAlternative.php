<?php

namespace PhatCats\Either\Alternative;

use PhatCats\Typeclass\Alternative;
use PhatCats\Typeclass\BaseApplicativeForObjectApplicative;
use PhatCats\Either\Either;

/*
 * An `Alternative` instance for `Either` does not exist in the Haskell
 * Prelude. I don't think the reason for this is obvious but
 * [this](https://stackoverflow.com/a/44472441) SO answer gives a hint.  It
 * seems that the issue is that the `Alternative` type class does not have the
 * appropriate constraints on the type variable for `Left` (and it's probably
 * correct that it doesn't).  It would need to look something like this:
 *
 * (Alternative e, Applicative (f e)) => Alternative (f e)) where . . .
 *
 * But that would be a strange type class indeed!
 *
 * In any case, we don't need to worry about such things here. The constructor
 * takes an `Applicative` that will act as the constraint on the `Left` type
 * variable.
 *
 * This `Alternative` instance has the following behavior, which is similar to
 * that of `Maybe` in Haskell:
 *
 * $firstRightEitherAlternative->append(a :: Left, b) = b
 * $firstRightEitherAlternative->append(a, b) = a
 *
 * In words, if the first arguemnt is a Left, the second argument is the result;
 * otherwise the first argument is the result.  Therefore, the wrapped values
 * are never appended.
 */
class FirstRightEitherAlternative extends BaseApplicativeForObjectApplicative implements Alternative {

  private $leftAlternative;

  public function __construct(Alternative $leftAlternative) {
    $this->leftAlternative = $leftAlternative;
  }

  function or($left, $right) {
    return $left->isLeft() ? $right : $left;
  }

  function empty() {
    return Either::left($this->leftAlternative->empty());
  }

  function pure($v) {
    return Either::fromValue($v);
  }
}
