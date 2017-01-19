<?php
/**
 * Base Example for rendering an Sub form
 */
function base() {
	//Loading factories
	global $DIC;
	$f = $DIC->ui()->factory();
	$renderer = $DIC->ui()->renderer();

	$items = [];
	$options = [];
	$options[] = $f->input()->item()->selector()->radioOption("radio_option1", "Radio 1");
	$options[] = $f->input()->item()->selector()->radioOption("radio_option2","Radio 2 Sub 2")->combineWithSubform(
		$f->input()->container()->form()->sub(
			[$f->input()->item()->field()->text("id15","Section Sub Sub Textfield 1")]
		));

	$items[] = $f->input()->item()->selector()->radioGroup("radio_group","Radio  Group", $options);


	$form = $f->input()->container()->form()->standard("#","Test Form",$items);

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
