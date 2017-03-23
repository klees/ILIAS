<?php
/**
 * Base Example for rendering a Form
 */
function base() {
	//Loading factories
	global $DIC;
	$f = $DIC->ui()->factory();
	$renderer = $DIC->ui()->renderer();

	//Collecting all inputs that will be stored in the form
	$items = [];

	//Minimal textfield
	$items[] = $f->input()->item()->field()->text("Minimal");

	//Required textfield with default value and standard validation
	$items[] = $f->input()->item()->field()->text("Required")
			->required();

	//Textfield with default value and standard validation
	$items[] = $f->input()->item()->field()->text("Default Value and Validation")
			->required()
			->withValue(1)
			->addValidation($f->input()->validation()->equals(3));

	//Required textfield with custom validation
	$items[] = $f->input()->item()->field()->text("Bigger than 3")
			->required()
			->withValue(1)->addValidation(
					$f->input()->validation()->custom(
							function ($input){
								return $input > 3;
							},"Not bigger than 3"));

	//Textfield with chained mapping. Will convert input to int, double it and wrap
	//some text around it.
	$items[] = $f->input()->item()->field()->text("Combined Mapping")
			->withValue(3)
			->addMapping(
					function($input){
						return intval($input)*2;
					})
			->addMapping(
					function($input){
						return "Output to Model is Input times 2: ".$input;
					});

	//Combined Input with chained mapping. First map into an array, then into some object.
	$structured_output = new stdClass();

	$item1 = $f->input()->item()->field()->text("Part of Combined Input");

	$items[] = $item1->combine($item1)
			->addMapping(
					function($input){
						return ["item 1"=>$input[0],"item 2"=>$input[1]];
					})
			->addMapping(
					function($input) use ($structured_output) {
						$structured_output->item1 = $input["item 1"];
						$structured_output->item2 = $input["item 2"];
					});

	//Create form with inputs
	$form = $f->input()->container()->form()->standard("#","Test Form",$items);


	//Show some output if form has been sent;
	$output = "";
	if($_POST){
		//Store post input into form. The input will be validated automatically.
		//Todo, the the key test is due to yet improper handling of keys.
		$form = $form->withInputFromView($_POST['test']);
		if($form->isValid()){
			$output .= "Array: </br>";
			$output .=print_r($form->map(),true)."</br>";
			$output .= "Object: </br>";
			$output .=print_r($structured_output,true)."</br>";
		}else{
			$output .= "Invalid Input</br>";

			foreach($form->getMessageCollector() as $message){
				//Todo: there is no clear concept yet for item name.
				$output .= $message->getItem()->getName().":</br>";
				$output .= $message->getMessage()."</br>";
			}
		}
	}

	return $renderer->render($form).$output;
}
