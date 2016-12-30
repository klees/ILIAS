<?php
require_once($_SERVER['DOCUMENT_ROOT']."/libs/composer/vendor/autoload.php");


use ILIAS\UI\Implementation\Component\Input\Formlet\FunctionValue\Factory as F;


$times = F::functionValue(function($a,$b){
	return $a*$b;
});

$double = F::functionValue(function($input){
	return $input*2;
});

$view_validation = $times->apply(3)->apply($double)->apply(4);

var_dump($view_validation->get());


$explode = F::functionValue("explode", 2);

// We could apply the function once to the delim, creating a new function.
$explodeBySpace = $explode->apply(" ");

// We could apply the function to the string to create the final result:
$res = $explodeBySpace->apply("foo bar");

// Since the value is still wrapped, we need to unwrap it.
$unwrapped = $res->get();

echo "Array containing \"foo\" and \"bar\":\n";
print_r($unwrapped);

$equals = F::functionValue(function($a){
	return $a!=$a;
});
$invert = F::invert();

var_dump($invert->apply($equals)->apply(3)->get());

$a = false;

var_dump($invert->apply($invert)->apply(false)->get());