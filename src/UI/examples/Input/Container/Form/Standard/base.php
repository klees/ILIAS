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
	$out = new stdClass();

	//Generating a text input with default content "test"
	$items[] = $f->input()->item()->field()->text("Textfield 1")
			->required(true)
			->withValue(1);

	//Generating a text input with default content 3. Output is validated to
	// equal 3 and finally doubled and enhanced with some text before passed
	// back to the model.
	$items[] = $f->input()->item()->field()->text("Textfield 2")
			->withValue(3)
			->addMapping(
					function($input){
						return "Output to Model is Input times 2: ".$input;
					})
			->addMapping(
					function($input){
						return intval($input)*2;
					})
			->addValidation($f->input()->validation()->equals(3));

	//Section with Name Age field
	//$items[] = $f->input()->container()->form()->section("Section 1",
    //    [$f->input()->item()->field()->nameAge("id3")]);

	//Stuff it all to the form

	/**
	$item1 = $f->input()->item()->field()->text("Textfield 3")
			->addMapping(
				function($input) use ($out){
					$out->content = $input;
				});

	$combined = $item1->combine($item1);
	$items[] = $combined;**/

	$form = $f->input()->container()->form()->standard("#","Test Form",$items);


	//Show some output if form has been sent;
	$output = "";
	if($_POST){
		$form = $form->withInputFromView($_POST['test']);
		if($form->isValid()){
			$output .=print_r($form->map(),true);
			$output .=print_r($out,true);

		}else{
			$output .= "Invalid Input";
		}
	}

	return $renderer->render($form).$output;
}
