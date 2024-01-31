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

namespace ILIAS\Component\Resource;

/**
 * An endpoint is a PHP file that produces output via HTTP. These will be located
 * on the toplevel of the public folder. Fall back to ComponentResource if something
 * else is needed.
 */
class Endpoint extends ComponentResource
{
    /**
     * @param $component this belongs to
     * @param $source path relative to the components resources directory
     */
    public function __construct(
        \ILIAS\Component\Component $component,
        string $source
    ) {
        parent::__construct($component, $source, ".");
    }
}
