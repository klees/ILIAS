<?php
/**
 * Base Example for rendering an Image
 */
function base() {
	//Loading factories
	global $DIC;
	$f = $DIC->ui()->factory();
	$renderer = $DIC->ui()->renderer();

	$items = [];

	//Genarating and rendering the text input
	$text_input = $f->input()->item()->field()->text("id1","Textfield 1");
	$items[] = $text_input->withInputFromModel(["id1"=>"test"])
			->required(true);

	$text_input = $f->input()->item()->field()->text("id2","Textfield 2")
			->withInputFromModel(["id2"=>6]);
	$text_input->addValidation($f->input()->validation()->equals(3));
	$text_input->addViewToModelMapping(function($input){
		return "Output to Model is Input times 2:".($input*2);
	});
	$items[] = $text_input->addModelToViewMapping(function($input){
		var_dump($input);
		exit;
		return number_format ($input, 2);
	});

	$form = $f->input()->container()->form()->standard("#","test",$items);

	$output = "";
	if($_POST){
		$form->withInputFromView($_POST);
		if($form->isValid()){
			foreach($form->extractToModel() as $out){
				$output .= $out;
			}

		}else{
			$output .= "Invalid Input";
		}
	}

	return $renderer->render($form).$output;
}
