<?php

namespace TMciver\Functional\Maybe;

use TMciver\Functional\Monad;

class MaybeT {
    use Monad;

    private $monad;

    public function __construct($monad) {
	$this->monad = $monad;
    }

    public function map(callable $f) {
	return new MaybeT($this->monad->map(function ($maybe) use ($f) {
	    return $maybe->map($f);
	}));
    }

    public function flatMap(callable $f) {
	$newMonad = $this->monad->flatMap(function ($maybe) use ($f) {
	    if ($maybe->isNothing()) {
		$newMonad = $this->monad->pure($maybe);
	    } else {
		$newMaybeT = $f($maybe->get());
		$newMonad = $newMaybeT->monad;
	    }

	    return $newMonad;
	});

	return new MaybeT($newMonad);
    }

    public function pure($val) {
	return new MaybeT($this->monad->pure(Maybe::fromValue($val)));
    }
}
