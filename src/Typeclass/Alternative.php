<?php

namespace PhatCats\Typeclass;

/**
 * A monoid on applicative functors.
 */
interface Alternative extends Applicative {

  /**
   * An associative binary operation 
   *
   * @param $left :: m a : The first action being considered.
   * @param $right :: m a : The second action being considered.
   * @return :: m a : The action resulting from "or"ing the two objects together.
   */
  function or($left, $right);

  /**
   * The identity of the `or` operation.
   *
   * @return $ma :: m a
   */
  function empty();

  /**
   * Repeatedly applies the action to return zero or more values.
   *
   * @param $v :: m a : an action to apply.
   * @return :: m (LinkedList a)
   */
  function zeroOrMore($v);

  /**
   * Repeatedly applies the action to return one or more values.
   *
   * @param $v :: m a : an action to apply.
   * @return :: m (LinkedList a)
   */
  function oneOrMore($v);
}
