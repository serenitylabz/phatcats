[![CircleCI](https://circleci.com/gh/tmciver/functional-php.svg?style=svg)](https://circleci.com/gh/tmciver/functional-php)

# Functional PHP

## Contents

* [About](#about)
* [Running Tests](#running-tests)
* [Typeclasses](#typeclasses)
* [Types](#types)
    * [LinkedList](#linkedlist)
    * [Maybe](#maybe)
        - [Accessing the Value](#accessing-the-wrapped-value)
        - [Maybe as Functor](#maybe-as-functor)
        - [Maybe as Monad](#maybe-as-monad)
        - [Maybe as Monoid](#maybe-as-monoid)
        - [Converting](#converting)
    * [Either](#either)
    * [MaybeT](#maybet)
    * [Validation](#validation)
    * [AssociativeArray](#associativearray)

## About

This library is designed to give PHP developers some Category-Theory-like
facilities available in a language like Haskell.

## Running Tests

### Natively

This project uses [Composer](getcomposer.org) for dependency management and
[PHPUnit](phpunit.de) for unit testing.  First, install the dependencies
(including dev dependencies):

    $ composer install --dev

PHPUnit requires access to autoloading and the autoloading files must first be
generated by composer with the following command:

    $ composer dump-autoload

Then, run the unit tests with

    $ ./vendor/bin/phpunit

### Using Docker

If you have docker installed you can run tests with:

    $ make test

## Typeclasses

The following typeclasses are supported:

* `SemiGroup`
* `Monoid`
* `Functor`
* `Monad`
* `Applicative`
* `Traversable`

Note that not all types support all typeclasses.

## Types

This library supports the following types:

* `LinkedList`
* `Maybe`
* `Either`
* `MaybeT`
* `Validation`
* `AssociativeArray`

Note that not all supported types are instances of the above type classes.

### LinkedList

The `LinkedList` type is an [abstract data type](https://en.wikipedia.org/wiki/Abstract_data_type) implementing a typical linked list data structure.

#### Creating

`LinkedList`'s should be created using an instance of the `LinkedListFactory` class. For example, you can create an empty list:

```php
$listFactory = new LinkedListFactory();

$emptyList = $listFactory->empty();
```

You can create a `LinkedList` from a PHP array like so:

```php
$arr = ['apples', 'oranges', 'bananas'];
$l = $listFactory->fromNativeArray($arr);
// $l = LinkedList('apples', 'oranges', 'bananas');
```

You can also easily create a range of values:

```php
$l = $listFactory->range('a', 'f', 2);
// $l = LinkedList('a', 'c', 'e');
```

#### LinkedList Monoid

`LinkedList`s can be `append`ed:

```php
$l1 = $listFactory->range(1, 2, 3);
$l2 = $listFactory->range(4, 5, 6);
$l3 = $l1->append($l2);
// $l3 = LinkedList(1, 2, 3, 4, 5, 6);
```

#### LinkedList Functor

As expected, `LinkedList`s are functors.  Simply pass a function of one
argument to the `map` method:

```php
$l = $listFactory->fromNativeArray([1, 2, 3]);
$linc = $l->map(function ($x) { return $x + 1; });
// $linc = LinkedList(2, 3, 4);
```

#### LinkedList Monad

They are also monads:

```php
$l = $listFactory->fromNativeArray(["Hello", "world"]);
$explodedStrs = $l->flatMap(function ($s) {
  return str_split($s);
});
// $explodedStrs = LinkedList('H', 'e', 'l', 'l', 'o', 'w', 'o', 'r', 'l', 'd');
```

#### LinkedList Applicative

The list applicative may be a little unintuitive if you've never seen it before.
It allows you to apply each function in a list of functions to each of a list of
arguments.  An example may make it a bit clearer.

```php
$firstThree = function ($s) { return substr($s, 0, 3); };
$fs = $listFactory->fromNativeArray(['strtoupper', $firstThree]);
$args = $listFactory->fromNativeArray(["Hello", "world"]);

$result = $fs->apply($args);
// $result = LinkedList("HELLO", "WORLD", "Hel", "wor");
```

The `__invoke` magic method can also be used to achieve the same result:

```php
$result = $fs($args);
// $result = LinkedList("HELLO", "WORLD", "Hel", "wor");
```

You can even call a `LinkedList` of no-argument functions:

```php
$one = function () { return 1; };
$fs = $listFactory->fromNativeArray(['time', $one]);
$vals = $fs();
// $vals = LinkedList(1527883005, 1);
```

#### LinkedList Traversable

The `traverse` method of the `Traversable` typeclass is another method that at
first may seem a little strange but is actually quite useful.  `traverse` takes
as its first argument a function that takes an element of the `LinkedList` and
returns some monad.  As its second argument ot takes an instance of that same
monad.  The return value of `traverse` is an instance of the monad wrapping a
`LinkedList` containing the values that were wrapped in monads returned by the
passed-in function.  That was a mouthful but it's more intuitive when seen in an
example.  First, let's define a function that returns a `Maybe`:

```php
$divideTwelveBy = function ($denom) {
  return ($den == 0) ?
    Maybe::nothing() :
	Maybe::fromValue(12 / $denom);
};
```

Then we'll traverse a list of integers with that function:

```php
$l = $listFactory->fromNativeArray([1, 2, 3, 4]);
$divisions = $l->traverse($divideTwelveBy);
// $divisions = Just(LinkedList(12, 6, 4, 3));
```

`traverse` is useful for when you want map over a `LinkedList` but the result of
doing so would give you a `LinkedList` of some monad.  Using `traverse` inverts
the `LinkedList` and the monad.

But note what happens in this example if one of the calls to `$divideTwelveBy`
returns `Nothing`:

```php
$l = $listFactory->fromNativeArray([1, 0, 3, 4]);
$divisions = $l->traverse($divideTwelveBy);
// $divisions = Nothing;
```

The `Traversable` typeclass also has a method `sequence` that is useful for the
situation when you already have a `LinkedList` of some monad:

```php
$l = $listFactory->fromNativeArray([
	Maybe::fromValue(1),
	Maybe::fromValue(2),
	Maybe::fromValue(3)
]);
$m = $l->sequence();
// $m = Just(LinkedList(1, 2, 3));
```

### Maybe

`Maybe` is intended to be used to represent the situation where there is the
possibility of having an absense of a value.  Typically, you would use `Maybe`
when you might ordinarily return a null value from a function.  Sometimes
`Maybe` is also used to represent an error condition.

`Maybe` is impelemented as an abstract class with two concrete sub-classes:
`Just` and `Nothing`.  But you cannot instantiate these sub-classes directly;
you must use static creation methods defined in the `Maybe` class.  If you want
to put a regular value in a `Maybe` context, use the `fromValue()` static method
like so:

```php
$maybeInt = Maybe::fromValue($myInt);
```

After the above code executes, `$maybeInt` will be an instance of `Just`,
_unless_ `$myInt` was null in which case `$maybeInt` will be an instance of
`Nothing`.  If you want to represent the absence of value, use the `nothing()`
static method:

```php
$maybeInt = Maybe::nothing();
```

#### Accessing the Wrapped Value

You may want to get direct access to the value wrapped in a `Maybe`.  This only
makes sense if you have a default value that can be used in the case that your
`Maybe` is `Nothing`.  In Haskell you would use the [`fromMaybe`
function](https://hackage.haskell.org/package/base-4.10.1.0/docs/Data-Maybe.html#v:fromMaybe)
to do this.  Here, you do this by calling the `getOrElse` method like so:

```php
// preferred
$a = Maybe::fromValue(5);
$b = Maybe::nothing();

$a->getOrElse(0);  // yields 5
$b->getOrElse(0);  // yields 0
```

#### Maybe as Functor

Another common desire is to simply apply a regular function to the value wrapped
in the `Maybe` and to have the returned value wrapped back up in another
`Maybe`.  A datatype used in this manner is known as a `Functor`.

The following code shows how you could convert the string "apples" to uppercase
while it's contained in a `Maybe`:

```php
$a = Maybe::fromValue('apples');
$maybeUppercase = $a->map('strtoupper');

// $maybeUppercase = Just('APPLES');
```

But if `$a` had been an instance of `Nothing`, then the result would have been
`Nothing()` and the `strtoupper` function would never have been run.  You can
also chain `map`s:

```php
$a = Maybe::fromValue('apples');
$maybeUppercaseOfFirstLetter = $a->map('strtoupper')
                                 ->map(function ($str) {
                                    return substr($str, 0, 1);
                                 });
                                 
// $maybeUppercaseOfFirstLetter = Just('A');
```

There are a couple of things to note here.  First, `map` takes a `callable`.  In
PHP `callable`s take several forms but one of them is a string and in the first
call to `map`, we passed in the string version of a built-in PHP function.  In
the second case we pass in an anonymous function, also a `callable`.  See [PHP's
documentation on
`callable`](http://php.net/manual/en/language.types.callable.php) for more
info. If calling a function using a string seems strange, good; it *is* strange!
:)

Second, `callable`s passed into `map` must be functions of one argument and
that argument will be the value _wrapped_ in the `Maybe`.  Third, the value
returned by this function will _automatically_ be wrapped back up in a `Maybe`.
So the result of calling `map` on a `Maybe` is again a `Maybe` which is what
allows us to chain calls to `map` this way.

#### Maybe as Monad

It's not uncommon that the function you want to apply to the value wrapped in
the `Maybe` itself returns a `Maybe`.  You can't simply use the `map` method
in this case.  To demonstrate why, let's first create a function that returns
`Maybe`.  A classic example is the function `head()` which returns the first
element of an array.  Strangely, PHP does not have such a function and the
recommended approach is not straitforward as evidenced by this StackOverflow
answer: http://stackoverflow.com/a/3771228.  But even if you use the convoluted
solution described there, you still have to deal with a possible `NULL` value
being returned in the case of an empty array.

The following function hides the complexity of getting the first element of an
array and returns a `Maybe` type so that we don't need to deal with `NULL`s:

```php
function head($array) {
   if (is_array($array)) {
      if (count($array) > 0) {
         $vals = array_values($array);
         $h = Maybe::fromValue($array[0]);
      } else {
         $h = Maybe::nothing();
      }
   } else {
      $h = Maybe::nothing();
   }

   return $h;
}
```

When given a non-empty array, the above function will return `Just($v)` where
`$v` is the first value of the array argument.  It will return `Nothing` in all
other cases.  See [this file](test/Maybe/HeadTest.php) for examples of using this
function.

To see why we can't use this function with `map()` let's expand on the example
we used above but instead of starting off with a string wrapped in a `Maybe`, we
have an array of string:

```php
$a = Maybe::fromValue(['apples', 'oranges', 'bananas']);
$b = $a->map('head');

// $b = Just(Just('apples'));
```

As you can see we're left with a `Just` inside of a `Just` and this is almost
certainly not what you're going to want, in general.  To fix this, we simple
need to use the `flatMap` method instead:

```php
$a = Maybe::fromValue(['apples', 'oranges', 'bananas']);
$b = $a->flatMap('head');

// $b = Just('apples');
```

A datatype used in this way is called a `Monad`.

#### Maybe as Monoid

You may find yourself in a situation where you want to combine several `Maybe`s
into one `Maybe`.  In Haskell you would do this the the [`mappend`
function](https://hackage.haskell.org/package/base-4.10.1.0/docs/Data-Monoid.html#v:mappend)
or the [`(<>)`
operator](https://hackage.haskell.org/package/base-4.10.1.0/docs/Data-Monoid.html#v:-60--62-)
Here, you can do this using the `append` method.  The following code shows how
this works.

```php
$just1 = Maybe::fromValue(1);
$just2 = Maybe::fromValue(2);
$nothing = Maybe::nothing();

$just1->append($nothing);   // Just(1);
$nothing->append($just1);   // Just(1);
$just1->append($just2);     // Just([1, 2]);
$just2->append($just1);     // Just([2, 1]);
$nothing->append($nothing); // Nothing();
```

#### Converting

It is extremely common to want to convert your `Maybe` into some other type.  In
Haskell you would use the [`maybe`
function](https://hackage.haskell.org/package/base-4.10.1.0/docs/Data-Maybe.html#v:maybe)
to achieve this.  This library does not have such a function but you can use
other techniques to achieve the same result.  For example, you may want to
convert a `Maybe` into an HTTP response.  You could use PHP's `instanceof`
operator like so:

```php
// Ugly, but gets the job done.
if ($myMaybe instanceof Just) {
   $myVal = $myMaybe->get();
   $response = response("<p>$myVal is: " . $myVal . ".</p>");
} else {
   $response = response("There was no value!", 400);
}
```

A slightly better but equivalent way is to use the provided `isNothing()`
method:

```php
// A little better.
if ($myMaybe->isNothing()) {
   $response = response("There was no value!", 400);
} else {
   $myVal = $myMaybe->get();
   $response = response("<p>$myVal is: " . $myVal . ".</p>");
}
```

The recommended way to convert a `Maybe` to something else is to use
the [Visitor Pattern](https://en.wikipedia.org/wiki/Visitor_pattern).  You
create a `Maybe` visitor by creating a class that implements the `MaybeVisitor`
interface like so:

```php
class MaybeToHttpResponse implements MaybeVisitor {

   public function visitJust($just) {
      $myVal = $just->get();
      return response("<p>$myVal is: " . $myVal . ".</p>");
   }

   public function visitNothing($nothing) {
      return response("There was no value!", 400);
   }
}
```

And you do the conversion by creating an instance of this visitor and passing it
to the `accept` method of the `Maybe`:

```php
$response = $myMaybe->accept(new MaybeToHttpResponse());
```

And that's it!

### Either

The `Either` datatype is used to represent one of two possible values.  `Either`
is very similar in functionality to `Maybe` - So similar in fact that I'm not
going to go into detail on its use since it would be almost identical to what
was presented for `Maybe`.  But I will note the differences here.

Where `Maybe` is used to signal a possible lack of a value, `Either` is often
used to signal the possibility of an error (though it is more general than
that).  The two sub-classes of the `Either` abstract class are the rather
unintuitively named `Left` and `Right`.  This is because the `Either` type is
actually more general than simply indicating an error; it can be used to return
any two possibilities.  We use `Either` in this library for no other reason than
because it's what Haskell does.

The `Right` subclass is used to signal a successful calculation.  You create an
instance like so:

```php
$myEither = Either::fromValue($someVal);
```

The `Left` subclass is used to signal an error and you create an instance like
so:

```php
$myEither = Either::left('Houston, we have a problem!');
```

Notice that we created a `Left` by passing a string containing an error
message.  This is a common way to use `Either` for signalling error but `Left`
can contain any type and we could have just as correctly (and possibly more
clearly) passed in a custom error or exception class.

These are the only significant differences with `Maybe`.

### MaybeT

TBD

### AssociativeArray

`AssociativeArray` is a simple wrapper around PHP's native array so array/list
processing methods could be added to it.  Currently it only contains an
implementation for the `Traversable` trait.

To create an instance of an `AssociativeArray`, one simply calls the
constructor:

```php
$aa = new AssociativeArray([1,2,3]);
```

#### AssociativeArray Traversable

There are two methods in the `Traversable` trait: `traverse()` and
`sequence()`.  `sequence()` is the simpler of the two so we'll start with that.
In the case of `Maybe`, `sequence()` is most-commonly used to convert an array
of `Maybe` to a `Maybe` of array as follows:

```php
$a = new AssociativeArray([Maybe::fromValue(1), Maybe::fromValue(2), Maybe::fromValue(3)]);
$m = Maybe::nothing();
$b = $a->sequence($m); // $b = Just([1,2,3]);
```

Note that an instance of an `Applicative` must be passed to the `sequence()`
method.  This is an unfortunate consquence of dynamic typing where the type of
the objects contained in the array is not known in the case of an empty array.

The `traverse()` method is similar except that it gives you the opportunity to
run a function on each value in the array.  To demonstrate this, we first define
a function that returns an `Either` type (see [Either](#either)):

```php
function divide($x, $y) {
   if ($y == 0) {
      $eitherResult = Either::left('Division by zero!');
   } else {
      $eitherResult = Either::fromValue($x/$y);
   }

   return $eitherResult;
}
```

Then we use that function in a call to `traverse()`:

```php
$dividend = 12;
$divisors = [2, 4, 6];
$intsArray = new AssociativeArray($divisors);
$m = Either::left('');
$eitherResults = $intsArray->traverse(function ($i) use ($dividend) {
    return divide($dividend, $i);
}, $m);

// $eitherResults = Right([6,3,2]);
```

Note that both `sequence()` and `traverse()` have the characteristic that if one
or more elements in the array is `Nothing` (in the case of `sequence()`) or if
the function passed in to `traverse()` evaluates to `Nothing` (in the case of
`traverse()`), then the result is also `Nothing`.  Let's show this by looking at
a slightly modified version of the above `sequence()` example:

```php
$a = new AssociativeArray([Maybe::fromValue(1), Maybe::nothing(), Maybe::fromValue(3)]);
$m = Maybe::nothing();
$b = $a->sequence($m); // $b = Nothing();
```
