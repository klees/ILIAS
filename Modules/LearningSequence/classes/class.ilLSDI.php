<?php declare(strict_types=1);

/* Copyright (c) 2021 - Nils Haagen <nils.haagen@concepts-and-training.de> - Extended GPL, see LICENSE */

use Pimple\Container;
use ILIAS\GlobalScreen\ScreenContext\ScreenContext;

class ilLSDI extends Container
{
    public function init(ArrayAccess $dic) : void
    {
        $this["db.filesystem"] = function ($c) : ilLearningSequenceFilesystem {
            return new ilLearningSequenceFilesystem();
        };

        $this["db.settings"] = function ($c) use ($dic) : ilLearningSequenceSettingsDB {
            return new ilLearningSequenceSettingsDB(
                $dic["ilDB"],
                $c["db.filesystem"]
            );
        };

        $this["db.activation"] = function ($c) use ($dic) : ilLearningSequenceActivationDB {
            return new ilLearningSequenceActivationDB($dic["ilDB"]);
        };

        $this["db.states"] = function ($c) use ($dic) : ilLSStateDB {
            return new ilLSStateDB($dic["ilDB"]);
        };

        $this["db.postconditions"] = function ($c) use ($dic) : ilLSPostConditionDB {
            return new ilLSPostConditionDB($dic["ilDB"]);
        };

        $this["gs.current_context"] = function ($c) use ($dic) : ScreenContext {
            return $dic->globalScreen()->tool()->context()->current();
        };
    }
}
