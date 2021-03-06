<?php

use PHPUnit\Framework\TestCase;
use PhatCats\Either\Either;
use PhatCats\Either\Left;

class EitherTest extends TestCase {

    public function testOrElseForLeft() {

        $either = Either::left('');
        $eitherNoException = $either->orElse(function () {
            throw new \Exception('I should be caught!');
        }, []);

        $this->assertInstanceOf(Left::class, $eitherNoException);
    }

    public function testOrElseForRight() {

        $either = Either::fromValue('');
        $eitherSame = $either->orElse(function () {
            return Either::fromValue('A new Either!');
        }, []);

        $this->assertEquals($either, $eitherSame);
    }
}
