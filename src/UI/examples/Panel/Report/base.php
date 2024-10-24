<?php declare(strict_types=1);

namespace ILIAS\UI\examples\Panel\Report;

function base()
{
    global $DIC;
    $f = $DIC->ui()->factory();
    $renderer = $DIC->ui()->renderer();

    $sub1 = $f->panel()->sub("Sub Panel Title 1", $f->legacy("Some Content"))
            ->withFurtherInformation($f->card()->standard("Card Heading")->withSections(array($f->legacy("Card Content"))));
    $sub2 = $f->panel()->sub("Sub Panel Title 2", $f->legacy("Some Content"));

    $block = $f->panel()->report("Report Title", array($sub1,$sub2));

    return $renderer->render($block);
}
