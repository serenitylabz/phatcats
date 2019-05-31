<?php

namespace PhatCats\Typeclass;

/**
 * A monoid on applicative functors.
 *
 * 
 */
interface Alternative extends Applicative {

  /**
   * An associative binary operation 
   *
   * @param $other :: m a : The other object being considered.
   * @return :: m a : The result of "or"ing the two objects together.
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
   * @return :: m (LinkedList a)
   */
  function zeroOrMore($v);

  /**
   * Repeatedly applies the action to return one or more values.
   *
   * @return :: m (LinkedList a)
   */
  function oneOrMore($v);
}
