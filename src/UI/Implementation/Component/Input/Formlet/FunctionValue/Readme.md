# Function Values

Function Values could be used as ordinary values, that is they
can be stored in a variable, being passed around or used as an argument to another function.
Functions in PHP in opposite are not that volatile, mostly you call them by their name.
You could off course use some PHP magic like $function_name() to come a bit closer to
the aforementioned property of functions and PHPs callable aims at this direction.

How to do something like explode(" ", $foo) then, you might wonder. Easy. Since
functions are ordinary values in our abstraction, you just create a function that takes
the delimiter and returns a function that splits a string at the delimiter:

```php
<?php
require_once($_SERVER['DOCUMENT_ROOT']."/libs/composer/vendor/autoload.php");
use ILIAS\UI\Implementation\Component\Input\Formlet\FunctionValue\Factory as F;

// Create a function object from an ordinary PHP function. Since explode takes
// two mandatory and one optional parameter, we have to explicitly tell how many
// optional parameters we want to have.
$explode = F::functionValue("explode", 2);

// We could apply the function once to the delim, creating a new function.
$explodeBySpace = $explode->apply(" ");

// We could apply the function to the string to create the final result:
$res = $explodeBySpace->apply("foo bar");

// Since the value is still wrapped, we need to unwrap it.
$unwrapped = $res->get();

echo "Array containing \"foo\" and \"bar\":\n";
print_r($unwrapped);
?>
```

See: [An implementation of the Formlets-abstraction in PHP](https://github.com/jbt/markdown-editor.com/lechimp-p/php-formlets).

Copyright (c) 2014 Richard Klees <richard.klees@rwth-aachen.de>