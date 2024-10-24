<?php declare(strict_types=1);

/* Copyright (c) 2018 Nils Haagen <nils.haagen@concepts-and-training.de> Extended GPL, see docs/LICENSE */

namespace ILIAS\UI\Component\Layout\Page;

use ILIAS\UI\Component\Breadcrumbs\Breadcrumbs;
use ILIAS\UI\Component\Image\Image;
use ILIAS\UI\Component\JavaScriptBindable;
use ILIAS\UI\Component\MainControls\SystemInfo;
use ILIAS\UI\Component\MainControls\MainBar;
use ILIAS\UI\Component\MainControls\MetaBar;
use ILIAS\UI\Component\MainControls\ModeInfo;
use ILIAS\UI\Component\MainControls\Footer;
use ILIAS\UI\Component\Toast\Container;

/**
 * This describes the Page.
 */
interface Standard extends Page, JavaScriptBindable
{
    //Possible Text Directions
    public const LTR = 'ltr';
    public const RTL = 'rtl';

    public function withMetabar(MetaBar $meta_bar) : Standard;

    public function withMainbar(MainBar $main_bar) : Standard;

    public function withLogo(Image $logo) : Standard;

    public function hasMetabar() : bool;

    public function hasMainbar() : bool;

    public function hasLogo() : bool;

    public function hasOverlay() : bool;

    public function getMetabar() : ?MetaBar;

    public function getMainbar() : ?MainBar;

    public function getBreadcrumbs() : ?Breadcrumbs;

    public function getLogo() : ?Image;

    public function getOverlay() : ?Container;

    public function getFooter() : ?Footer;

    public function withTitle(string $title) : Standard;

    public function getTitle() : string;

    public function withShortTitle(string $title) : Standard;

    public function getShortTitle() : string;

    public function withViewTitle(string $title) : Standard;

    public function getViewTitle() : string;

    public function withModeInfo(ModeInfo $mode_info) : Standard;

    public function getModeInfo() : ?ModeInfo;

    public function hasModeInfo() : bool;

    /**
     * @param SystemInfo[] $system_infos
     */
    public function withSystemInfos(array $system_infos) : Standard;

    /**
     * @return SystemInfo[]
     */
    public function getSystemInfos() : array;

    public function hasSystemInfos() : bool;

    /**
     * Set the direction of the text. This is used in CSS.
     * Note that in the default skin, rtl is only partly supported.
     */
    public function withTextDirection(string $text_direction) : Standard;

    /**
     * Get the direction of the text. This is used in CSS.
     * Note that in the default skin, rtl is only partly supported.
     */
    public function getTextDirection() : string;
}
