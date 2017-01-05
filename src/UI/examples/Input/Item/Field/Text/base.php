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
	$text_input = $text_input->addValidation(
			$f->input()->validation()->equals(3));


	$text_input->withInputFromView(["id"=>"testView"]);

	$html .= $renderer->render($text_input);

	$text_input->withInputFromView(["id"=>"3"]);

	$html .= $renderer->render($text_input);

	return $html;
}
