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

namespace ILIAS\Component\Dependencies\ResolutionDirective;

use ILIAS\Component\Dependencies\ResolutionDirective;

/**
 * Tells the Resolver to use an implementation with a certain class name to
 * resolve a certain dependency.
 *
 * Specifity is 1.
 */
class ForXUseY implements ResolutionDirective
{
    public function __construct(
        protected string $x,
        protected string $y,
    ) {
    }

    public function getX(): string
    {
        return $this->x;
    }

    public function getY(): string
    {
        return $this->y;
    }

    public function getSpecificity(): int
    {
        return 1;
    }
}
