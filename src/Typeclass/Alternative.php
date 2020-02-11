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
}
