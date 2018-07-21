<?php

namespace TMciver\Functional\Maybe;

use TMciver\Functional\Maybe\Maybe;
use TMciver\Functional\Maybe\Nothing;
use TMciver\Functional\AssociativeArray;
use TMciver\Functional\PartialFunction;
use TMciver\Functional\Util;
use TMciver\Functional\Typeclass\SemiGroup;

class Just extends Maybe {

  private $val;

    public function __construct($val) {
	$this->val = $val;
    }

    public function getOrElse($default) {
        return $this->val;
    }

    public function map(callable $f) {

	// Since we don't know if $f will throw an exception, we wrap the call
	// in a try/catch. The result wiil be Nothing if there's an exception.
	try {
            $ret = call_user_func($f, $this->val);
	    $maybeResult = Maybe::fromValue($ret);
	} catch (\Exception $e) {
	    $maybeResult = self::fail();
	}

	return $maybeResult;
    }

    public function flatMap(callable $f) {

	// Since we don't know if $f will throw an exception, we wrap the call
	// in a try/catch. The result wiil be Nothing if there's an exception.
	try {
	    $maybeResult = $f($this->val);

	    // If the result is null, we return Nothing.
	    if (is_null($maybeResult)) {
		$maybeResult = self::fail();
	    }
	} catch (\Exception $e) {
	    $maybeResult = self::fail();
	}

	return $maybeResult;
    }

    protected function applyNoArg() {
      return new Just(call_user_func($this->val));
    }

    protected function applyToArg($maybeArg) {
      return $maybeArg->applyToJust($this);
    }

    protected function applyToJust($just) {
      // Wrap the applicative value in a PartialFunction,
      // if it is not already.
      $pf = $just->val instanceof PartialFunction ?
	$just->val :
	new PartialFunction($just->val);

      return $this->map($pf);
    }

    public function append($appendee, SemiGroup $semiGroup) {
      return $appendee->appendJust($this, $semiGroup);
    }

    protected function appendJust($just, SemiGroup $semiGroup) {
        // this is where the real work of appending two Just's is done.

        // Since we can't know if the value contained in a Maybe is itself a
        // monoid, we're just going to put the values in an array. But there are
        // four cases that we have to account for to create the proper result
        // array so that associativity is maintained.
        $leftVal = $just->val;
        $rightVal = $this->val;

        $appendedResult  = $semiGroup->append($leftVal, $rightVal);

        // if (is_string($leftVal) && is_string($rightVal)) {
        //   $appendedResult = $leftVal . $rightVal;
        // } else if (Util::is_monoid($leftVal) && Util::is_monoid($rightVal)) {
        //   $appendedResult = $leftVal->append($rightVal);
        // } else if (!is_array($leftVal) && !is_array($rightVal)) {
        //   $appendedResult = [$leftVal, $rightVal];
        // } else if (is_array($leftVal) && !is_array($rightVal)) {
        //   $leftVal[] = $rightVal;
        //   $appendedResult = $leftVal;
        // } else if (!is_array($leftVal) && is_array($rightVal)) {
        //   array_unshift($rightVal, $leftVal);
        //   $appendedResult = $rightVal;
        // } else {
        //   // both values are arrays
        //   $appendedResult = array_merge($leftVal, $rightVal);
        // }

        return Maybe::fromValue($appendedResult);
    }

    public function accept($maybeVisitor) {
	return $maybeVisitor->visitJust($this);
    }

    public function get() {
	return $this->val;
    }

    public function orElse(callable $f, array $args) {
	return $this;
    }

    public function isNothing() {
	return false;
    }
}
