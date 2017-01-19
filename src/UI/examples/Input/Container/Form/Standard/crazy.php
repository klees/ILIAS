<?php
/**
 * Base Example for rendering a Form
 */
function crazy() {
	//Loading factories
	global $DIC;
	$f = $DIC->ui()->factory();
	$renderer = $DIC->ui()->renderer();

	$item1 = $f->input()->item()->field()->text("id1","Textfield 1")
			->withInputFromModel(["id1"=>"test"])
			->required(true);

	$combination = $item1->combine($item1->createClone("id2"));

	$form = $f->input()->container()->form()->standard("#","Test Form",
			[$combination]);

	return $renderer->render($form);
}
