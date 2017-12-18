<?php

use TMciver\Functional\PartialFunction;

//require_once __DIR__ . '/../util.php';

class PartialFunctionTest extends PHPUnit_Framework_TestCase {

  public function testInvokeWithArgsAtCallTime() {

    $pf = new PartialFunction(add);
    $result = $pf(1, 2);
    $expectedResult = 3;

    $this->assertEquals($expectedResult, $result);
  }

  public function testInvokeWithArgsAtCreationTime() {

    $pf = new PartialFunction(add, [1, 2]);
    $result = $pf();
    $expectedResult = 3;

    $this->assertEquals($expectedResult, $result);
  }

  public function testInvokeWithArgsOneAtATime() {

    $pf = new PartialFunction(substr);
    $pf2 = $pf('hello world!');
    $pf3 = $pf2(6);
    $result = $pf3(5);
    $expectedResult = 'world';

    $this->assertEquals($expectedResult, $result);
  }
}
