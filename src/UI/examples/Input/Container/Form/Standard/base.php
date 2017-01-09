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
    $options2 = [];
    $options2[] = $f->input()->item()->selector()->radioOption("id13","Radio Sub 1");
    $options2[] = $f->input()->item()->selector()->radioOption("id12","Radio Sub 2")->combineWithSubform(
        $f->input()->container()->form()->sub(
            [$f->input()->item()->field()->text("id15","Section Sub Sub Textfield 1")]
        ));
    $group = $f->input()->item()->selector()->radioGroup("id3","Radio Group Sub ", $options2);

    $options = [];
    $options[] = $f->input()->item()->selector()->radioOption("id1","Radio 1");
    $options[] = $f->input()->item()->selector()->radioOption("id2","Radio 2")->combineWithSubform(
        $f->input()->container()->form()->sub(
            [$f->input()->item()->field()->text("id5","Section Textfield 1"),$group]
        ));

    $items[] = $f->input()->item()->selector()->radioGroup("id103","Radio Group", $options);
    /**
	//Genarating and rendering the text input
	$text_input = $f->input()->item()->field()->text("id1","Textfield 1");

	$items[] = $text_input->withInputFromModel(["id1"=>"test"])
			->required(true);

	$text_input = $f->input()->item()->field()->text("id2","Textfield 2");
    $text_input = $text_input->addValidation($f->input()->validation()->equals(3));
    $text_input = $text_input->addViewToModelMapping(function($input){
		return "Output to Model is Input times 2:".($input*2);
	});
	$items[] = $text_input->addModelToViewMapping(function($input){
		return number_format ($input, 2);
	})->withInputFromModel(["id2"=>6]);

    $items[] = $f->input()->container()->form()->section("Section 1",
        [$f->input()->item()->field()->text("id4","Section Textfield 1")]);

    $items[] = $f->input()->item()->field()->text("id7","Section Textfield 1")->combineWithSubform(
        $f->input()->container()->form()->sub(
        [$f->input()->item()->field()->text("id5","Section Textfield 1")]
        ));

**/
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
