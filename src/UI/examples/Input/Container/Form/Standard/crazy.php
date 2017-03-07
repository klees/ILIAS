<?php
/**
 * Base Example for rendering a Form
 */
function crazy() {
	//Loading factories
	global $DIC;
	$f = $DIC->ui()->factory();
	$renderer = $DIC->ui()->renderer();

	$item1 = $f->input()->item()->field()->text("Textfield 1");

	$item2 = $f->input()->item()->field()->text("Textfield 2");

	$combined = $item1->combine($item2)->addMapping(
			function($input){
				$items = [];
				$items['item1'] = $input[0];
				$items['item2'] = $input[1];
				return $items;
			});

	$form = $f->input()->container()->form()->standard("#","Test Form",[$combined]);

	$output = "";
	if($_POST){
		$form = $form->withInputFromView($_POST['test']);
		if($form->isValid()){
			$output .=print_r($form->map(),true);
		}else{
			$output .= "Invalid Input";
		}
	}

	return $renderer->render($form).$output;
}
