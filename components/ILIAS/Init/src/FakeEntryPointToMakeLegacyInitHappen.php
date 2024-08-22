<?php

/**
 * This file is part of ILIAS, a powerful learning management system
 * published by ILIAS open source e-Learning e.V.
 *
 * ILIAS is licensed with the GPL-3.0,
 * see https://www.gnu.org/licenses/gpl-3.0.en.html
 * You should have received a copy of said license along with the
 * source code, too.
 *
 * If this is not the case or you just want to try ILIAS, you'll find
 * us at:
 * https://www.ilias.de
 * https://github.com/ILIAS-eLearning
 *
 *********************************************************************/

declare(strict_types=1);

namespace ILIAS\Init;

use ILIAS\Component\EntryPoint;

class FakeEntryPointToMakeLegacyInitHappen implements EntryPoint
{
    public function __construct(
        protected ILIAS\Refinery\Factory $refinery,
        protected ILIAS\UI\Factory $ui_factory,
        protected ILIAS\UI\Renderer $ui_renderer,
    ) {
    }

    public function getName()
    {
        return "This is an adapter to make the legacy initialisation happen, not the way we want to do this in the future. Please look into docs/development/components-and-directories.md for further information.";
    }

    public function enter()
    {
        $GLOBALS["DIC"] = new Container();
        $DIC = $GLOBALS["DIC"];
        $DIC["ui.factory"] = $this->ui_factory;
        $DIC["ui.renderer"] = $this->ui_renderer;
        $DIC["refinery"] = $this->refinery;

        ilInitialisation::initILIAS();
    }
}
