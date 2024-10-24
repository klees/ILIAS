<?php declare(strict_types=1);

namespace ILIAS\UI\examples\MainControls\Slate\Combined;

function combined()
{
    global $DIC;
    $f = $DIC->ui()->factory();
    $renderer = $DIC->ui()->renderer();

    $icon = $f->symbol()->glyph()->comment();
    $contents = $f->legacy("some contents.");
    $slate1 = $f->maincontrols()->slate()->legacy('legacy1', $icon, $contents);
    $slate2 = $f->maincontrols()->slate()->legacy('legacy2', $icon, $contents);
    $divider = $f->divider()->horizontal()->withLabel('Horizontal Divider with Text');

    $glyph = $f->symbol()->glyph()->briefcase();
    $button = $f->button()->bulky($glyph, 'Button', '#');

    $slate = $f->maincontrols()->slate()
        ->combined('combined_example', $f->symbol()->glyph()->briefcase())
        ->withAdditionalEntry($slate1)
        ->withAdditionalEntry($button)
        ->withAdditionalEntry($divider)
        ->withAdditionalEntry($slate2);


    $triggerer = $f->button()->bulky(
        $slate->getSymbol(),
        $slate->getName(),
        '#'
    )
    ->withOnClick($slate->getToggleSignal());

    return $renderer->render([
        $triggerer,
        $slate
    ]);
}
