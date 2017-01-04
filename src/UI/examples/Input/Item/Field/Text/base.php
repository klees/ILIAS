<?php
/**
 * Base Example for rendering an Image
 */
function base() {
	//Loading factories
	global $DIC;
	$f = $DIC->ui()->factory();
	$renderer = $DIC->ui()->renderer();

	//Genarating and rendering the text input
	$text_input = $f->input()->item()->field()->text("id","Textfield");
	$text_input->withInputFromModel(["id"=>"test"]);
	$html = $renderer->render($text_input);

	$text_input = $f->input()->item()->field()->text("id","Textfield");
	$text_input = $text_input->addValidation((new
	\ILIAS\UI\Implementation\Component\Input\Validation\Validation(function($input){
		return $input == 3;//is_numeric($input);
	},"Error1")));
	$text_input->withInputFromView(["id"=>"testView"]);

	$html .= $renderer->render($text_input);

	return $html;
}
