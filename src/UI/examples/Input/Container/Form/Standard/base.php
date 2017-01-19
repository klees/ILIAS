<?php
/**
 * Base Example for rendering a Form
 */
function base() {
	//Loading factories
	global $DIC;
	$f = $DIC->ui()->factory();
	$renderer = $DIC->ui()->renderer();

	$items = [];

	//Genarating a text input with default content "test"
	$items[] = $f->input()->item()->field()->text("id1","Textfield 1")
			->withInputFromModel(["id1"=>"test"])
			->required(true);

	//Genarating a text input with default content 3, which is transformed to
	// 1.50 for the view by the ModelToViewMapper. Output is validated to
	// equal 3 and finally doubled and enhanced with some text before passed
	// back to the model.
	$test = new stdClass();
	$items[] = $f->input()->item()->field()->text("id2","Textfield 2")
			->addModelToViewMapping(
					function($input){
						return number_format ($input, 2);
					})
			->addModelToViewMapping(
					function($input){
						return $input/2;
					})
			->withInputFromModel(["id2"=>3])
			->addViewToModelMapping(
					function($input) use ($test){
						$test->content = "Test: ".$input;
						return "Output to Model is Input times 2: ".$input;
					})
			->addViewToModelMapping(
					function($input){
						return $input*2;
					})
			->addValidation($f->input()->validation()->equals(3));

	//Section with Name Age field
	//$items[] = $f->input()->container()->form()->section("Section 1",
    //    [$f->input()->item()->field()->nameAge("id3")]);

	//Stuff it all to the form
	$form = $f->input()->container()->form()->standard("#","Test Form",$items);


	//Show some output if form has been sent;
	$output = "";
	if($_POST){
		$form->withInputFromView($_POST);
		if($form->isValid()){
			foreach($form->extractToModel() as $out){
				$output .= $out."</br>";
			}

		}else{
			$output .= "Invalid Input";
		}
		$output .= "Test: ".$test->content."</br>";
	}

	return $renderer->render($form).$output;
}
