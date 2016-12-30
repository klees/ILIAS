<?php
function show_modal_on_button_click()
{
    global $DIC;
    $factory = $DIC->ui()->factory();
    $renderer = $DIC->ui()->renderer();
    $content = $factory->legacy('This modal was opened on button click');
    $modal = $factory->modal()->interruptive('Modal Title', $content)
        ->withActionButton($factory->button()->primary('Delete', ''));
    $button = $factory->button()->standard('Open Modal', '');

    return $renderer->render($button->triggerAction($modal->show()));
}

$button->triggerAction($modal->showAsync($returnURL));


function showAsync($returnURL)
{
    $action = new TriggerAction($this);
    $action->setJavascriptBinding(function ($id) {
        return "il.Async.get($returnURL);";
    });

    return $action;
}


