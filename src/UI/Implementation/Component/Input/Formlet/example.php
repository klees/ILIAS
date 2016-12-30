<?php
require_once($_SERVER['DOCUMENT_ROOT']."/libs/composer/vendor/autoload.php");


use \ILIAS\UI\Implementation\Component\Input\Formlet\Internal\Refactor as R;
use \ILIAS\UI\Implementation\Component\Input\Formlet\Internal\Value as V;
use \ILIAS\UI\Implementation\Component\Input\Formlet\Factory as F;


$formlet = new R\Simple("test");
$formlet = $formlet->addViewToModelMapping(
		function($input){
			var_dump("in Function");
			return 2*$input;
		}
);
$formlet = $formlet->addValidation((new R\Validation(function($input){
	return $input == 3;//is_numeric($input);
},"Error1")));


$formlet = $formlet->combine($formlet);

$formlet = $formlet->withInputFromView(3);
var_dump($formlet->getMessageCollector()->getMessages());

var_dump($formlet->extractToView());
var_dump($formlet->extractToModel());
var_dump($formlet->extractToModel());
exit;

$formlet = $formlet->withInputFromView("asdf asdf");
var_dump($formlet->extractToModel());
var_dump($formlet->isValid());





$value = F::getFactory()->value()->plain("1000000");

$view_to_model = new V\FunctionCallable(function($input){
	return intval($input);
});

$model_to_view = new V\FunctionCallable(function($input){
	return "Value: ".$input;

});
$model_to_view = $model_to_view->composeWith(new V\FunctionCallable(function
($input){
	return number_format ($input);
}));

$map = new R\Map($model_to_view,$view_to_model);


var_dump($map->viewToModel($value));
var_dump($map->modelToView($value));



$view_validation = new V\FunctionCallable(function($input){
	return is_numeric($input);
});

$model_validation = new V\FunctionCallable(function($input){
	return is_int($input);

});

$validation = new R\Validation($view_validation,$model_validation);

$value1 = F::getFactory()->value()->plain("1000000");

$value2= F::getFactory()->value()->plain(1000000);

var_dump($validation->validateView($value1));
var_dump($validation->validateModel($value2));
