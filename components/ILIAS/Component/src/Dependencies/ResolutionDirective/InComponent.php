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
 * Tells the Resolver to apply that directives only in the given component.
 *
 * Specifity is one more than the maximum specifity of the included directives.
 */
class InComponent implements ResolutionDirective
{
    protected int $specificity;
    protected array $directives;

    public function __construct(
        protected string $component_name,
        ResolutionDirective ...$directives
    ) {
        usort($directives, fn($l, $r) => $l->getSpecificity() <=> $r->getSpecificity());
        $this->directives = $directives;
        $this->specificity = max(array_map(fn($d) => $d->getSpecificity(), $directives)) + 1;
    }

    public function getComponentName(): string
    {
        return $this->component_name;
    }

    public function getDirectives(): array
    {
        return $this->directives;
    }

    public function getSpecificity(): int
    {
        return $this->specificity;
    }
}
