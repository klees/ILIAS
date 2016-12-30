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
	$text_input = $f->input()->item()->field()->text("Textfield");
	$html = $renderer->render($text_input);

	return $html;
}
