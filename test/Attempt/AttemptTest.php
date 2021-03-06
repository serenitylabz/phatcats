<?php

use PHPUnit\Framework\TestCase;
use PhatCats\Attempt\Attempt;
use PhatCats\Attempt\Failure;

class AttemptTest extends TestCase {

	public function testAttemptGetOrElseForSuccess() {
		$try = Attempt::fromValue("Hello!");
		$val = $try->getOrElse("World!");

		$this->assertEquals("Hello!", $val);
	}

	public function testAttemptGetOrElseForFailure() {
		$try = Attempt::failure('');
		$val = $try->getOrElse("World!");

		$this->assertEquals("World!", $val);
	}

	public function testOrElseForFailure() {
		$try = Attempt::failure('');
		$tryNoException = $try->orElse(function () {
			throw new \Exception('I should be caught!');
		}, []);

		$this->assertInstanceOf(Failure::class, $tryNoException);
	}

	public function testOrElseForSuccess() {
		$try = Attempt::fromValue('');
		$trySame = $try->orElse(function () {
			return Attempt::fromValue('A new Attempt!');
		}, []);

		$this->assertEquals($try, $trySame);
	}

	public function testAttemptErrorMessage() {
		$errMsg = 'No power in the \'verse can stop me.';
		$try = Attempt::fromValue(null, $errMsg);
		$expected = Attempt::failure($errMsg);

		$this->assertEquals($expected, $try);
	}

	public function testCatch() {
		$err = Attempt::failure("Wump wump");
		$val = $err->catch(function($e) {
			return Attempt::fromValue(42);
		});
		$expected = Attempt::fromValue(42);

		$this->assertEquals($expected, $val);
	}
}
