<?php

namespace PhatCats\Typeclass;

interface Monad extends Applicative {

  /**
   * @param $ma: A value of type `a` in a context `m`.
   * @param $f :: a -> m b: a function that takes a value of type `a` and
   *        returns a value of type `b` in a context `m`.
   * @return A value of type `b` in a context `m`.
   */
  function flatMap($ma, callable $f);

  /**
   * Sequentially compose two actions, discarding any value produced by the
   * first, like sequencing operators (such as the semicolon) in imperative
   * languages.
   *
   * This is the same as the (>>) function in Haskell.  In fact, the above
   * description was take directly from its documentation
   * (http://hackage.haskell.org/package/base-4.12.0.0/docs/Control-Monad.html#v:-62--62-)
   *
   * @param $ma :: m a: The action to be composed after this one.
   * @return $ma :: m a: A new action that is the composition of this action
   * with the given action.
   */
  function then($ma);

  /**
   * Flattens a nested context.
   * @param $mma :: m (m a) A value in a context m which itself is in a context
   *        m.
   * @return a value of type `a` in a context `m`.
   */
  function join($mma);

  //function fail($messag);
}
