<?php declare(strict_types=1);

namespace ILIAS\UI\examples\Input\Field\Password;

/**
 * Example of how to process passwords.
 * Note that the value of Password is a Data\Password, not a string-primitive.
 */
function in_form()
{
    //Step 0: Declare dependencies
    global $DIC;
    $ui = $DIC->ui()->factory();
    $renderer = $DIC->ui()->renderer();
    $request = $DIC->http()->request();

    //Step 1: Define the input field.
    $pwd_input = $ui->input()->field()->password("Password", "Value will be displayed...")
        ->withRevelation(true);

    //Step 2: Define the form and attach the field.
    $form = $ui->input()->container()->form()->standard('#', ['password' => $pwd_input]);

    //Step 3: Define some data processing.
    $result = '';
    if ($request->getMethod() == "POST") {
        $form = $form->withRequest($request);
        $result = $form->getData();
    }

    //Step 4: Render the form/result.
    return
        "<pre>" . print_r($result, true) . "</pre><br/>" .
        $renderer->render($form);
}
