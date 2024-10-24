<?php declare(strict_types=1);

/* Copyright (c) 2016 Timon Amstutz <timon.amstutz@ilub.unibe.ch> Extended GPL, see docs/LICENSE */

namespace ILIAS\UI\Implementation\Component\Panel;

use ILIAS\UI\Implementation\Component\ViewControl\HasViewControls;
use ILIAS\UI\Component as C;

/**
 * Class Standard
 * @package ILIAS\UI\Implementation\Component\Standard
 */
class Standard extends Panel implements C\Panel\Standard
{
    use HasViewControls;
}
