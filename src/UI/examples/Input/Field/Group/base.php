<?php declare(strict_types=1);

namespace ILIAS\UI\examples\Input\Field\Group;

/**
 * Example showing how groups can be used to attach transformation and constraints on
 * multiple fields at once. Note that groups do not have a defined way of outputting
 * validations errors. This is context dependant.
 */
function base()
{
    //Step 0: Declare dependencies
    global $DIC;
    $ui = $DIC->ui()->factory();
    $lng = $DIC->language();
    $renderer = $DIC->ui()->renderer();
    $request = $DIC->http()->request();
    $data = new \ILIAS\Data\Factory();
    $refinery = $DIC->refinery();

    //Step 1: Implement transformation and constraints
    $sum = $refinery->custom()->transformation(function ($vs) {
        list($l, $r) = $vs;
        $s = $l + $r;
        return $s;
    });
    $equal_ten = $refinery->custom()->constraint(function ($v) {
        return $v == 10;
    }, "The sum must equal ten.");

    //Step 2: Define inputs
    $number_input = $ui->input()->field()->numeric("number", "Put in a number.");

    //Step 3: Define the group, add the inputs to the group and attach the
    //transformation and constraint
    $group = $ui->input()->field()->group(
        [ $number_input->withLabel("Left"), $number_input->withLabel("Right")]
    )
        ->withAdditionalTransformation($sum)
        ->withAdditionalTransformation($equal_ten);

    //Step 4: define form and form actions, attach the group to the form
    $form = $ui->input()->container()->form()->standard('#', ["custom_group" => $group]);

    //Step 4: Implement some form data processing.
    if ($request->getMethod() == "POST") {
        //Step 4.1: Device some context dependant logic to display the potential
        // constraint error on the group.
        $form = $form->withRequest($request);
        $group = $form->getInputs()["custom_group"];
        if ($group->getError()) {
            $result = $group->getError();
        } else {
            //The result is sumarized through the transformation
            $result = $form->getData();
        }
    } else {
        $result = "No result yet.";
    }

    //Step 5: Return the rendered form
    return
        "<pre>" . print_r($result, true) . "</pre><br/>" .
        $renderer->render($form);
}
