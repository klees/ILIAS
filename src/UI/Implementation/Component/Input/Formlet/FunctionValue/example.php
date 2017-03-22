<?php
require_once($_SERVER['DOCUMENT_ROOT']."/libs/composer/vendor/autoload.php");


use ILIAS\UI\Implementation\Component\Input\Formlet\FunctionValue\Factory as F;


//Find more examples in ./tests/UI/Input/FunctionValueTest.php

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

