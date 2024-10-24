<?php declare(strict_types=1);

namespace ILIAS\UI\examples\Listing\Workflow\Linear;

function base()
{
    //init Factory and Renderer
    global $DIC;
    $f = $DIC->ui()->factory()->listing()->workflow();
    $renderer = $DIC->ui()->renderer();

    //setup steps
    $step = $f->step('', '');
    $steps = [
        $f->step('step 1', 'available, successfully completed')
            ->withAvailability($step::AVAILABLE)->withStatus($step::SUCCESSFULLY),
        $f->step('step 2', 'available, successfully completed')
            ->withAvailability($step::AVAILABLE)->withStatus($step::SUCCESSFULLY),
        $f->step('step 3', 'available, in progress, active ')
            ->withAvailability($step::AVAILABLE)->withStatus($step::IN_PROGRESS),
        $f->step('step 4', 'not available, not started')
            ->withAvailability($step::NOT_AVAILABLE)->withStatus($step::NOT_STARTED),
        $f->step('step 5', 'not available, not started')
            ->withAvailability($step::NOT_AVAILABLE)->withStatus($step::NOT_STARTED)
    ];

    //setup linear workflow
    $wf = $f->linear('Linear Workflow', $steps)
        ->withActive(2);

    //render
    return $renderer->render($wf);
}
