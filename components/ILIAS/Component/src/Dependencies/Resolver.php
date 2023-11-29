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

namespace ILIAS\Component\Dependencies;

use ILIAS\Component\Dependencies\ResolutionDirective as RD;

class Resolver
{
    // Dependencies are resolved recursively, we capture where we are currently
    // at. Will contain pairs (OfComponent, In-Dependency)
    protected array $stack;

    // Components to be resolved.
    protected array $components;

    // Directives to resolve dependencies.
    protected array $directives;

    /**
     * Resolves dependencies of all components. Use ResolutionDirective to dis-
     * ambiguate use/implement-pairs or force special resolutions for certain
     * circumstances.
     *
     * @param array<ResolutionDirective> $directives
     * @param OfComponent[]
     * @return OfComponent[]
     */
    public function resolveDependencies(array $directives, OfComponent ...$components): array
    {
        usort($directives, fn($l, $r) => $l->getSpecificity() <=> $r->getSpecificity());

        $this->directives = $directives;
        $this->components = $components;

        $cycles = iterator_to_array($this->resolveDependenciesSeed());
        if (!empty($cycles)) {
            throw new \LogicException(
                "Detected Cycles in Dependency Tree: " .
                join("\n", array_map(
                    fn($cycle) => join(
                        " <- ",
                        array_map(
                            fn($v) => "{$v[0]->getComponentName()} ({$v[1]})",
                            $cycle
                        )
                    ),
                    $cycles
                ))
            );
        }

        // TODO: Make this return void, modifies components in place.
        return $components;
    }

    /**
     * @return Generator<array<OfComponent, Dependency>> cycles in the dependency graph
     */
    protected function resolveDependenciesSeed(): \Generator
    {
        foreach ($this->components as $component) {
            foreach ($component->getInDependencies() as $in) {
                yield from $this->resolveDependency([], $component, $in);
            }
        }
    }

    /**
     * @return Generator<array<OfComponent, Dependency>> cycles in the dependency graph
     */
    protected function resolveDependency(array $visited, OfComponent $component, In $in): \Generator
    {
        // Since SEEK-dependencies might in fact have no resolution and this
        // is also fine, this would lead to these dependencies be checked
        // again, even if that is not necessary. We take this potential
        // trade off for some simplicity.
        if ($in->isResolved()) {
            return;
        }

        // This is a cycle, we arrived where we started.
        if (!empty($visited) && $visited[0][0] === $component && $visited[0][1] == $in) {
            yield $visited;
            return;
        }

        array_push($visited, [$component, $in]);
        yield from match ($in->getType()) {
            InType::PULL => $this->resolvePull($visited, $component, $in),
            InType::SEEK => $this->resolveSeek($visited, $component, $in),
            InType::USE => $this->resolveUse($visited, $component, $in),
            default => throw new \LogicException("Unknown type: {$in->getType()}")
        };
        array_pop($visited);
    }


    protected function resolvePull(array $visited, OfComponent $component, In $in): \Generator
    {
        $candidate = null;

        foreach ($this->components as $other) {
            if ($other->offsetExists("PROVIDE: " . $in->getName())) {
                if (!is_null($candidate)) {
                    throw new \LogicException(
                        "Dependency {$in->getName()} is provided (at least) twice."
                    );
                }
                // For PROVIDEd dependencies, there only ever is one implementation.
                $candidate = $other["PROVIDE: " . $in->getName()][0];
            }
        }

        if (is_null($candidate)) {
            throw new \LogicException("Could not resolve dependency for: " . (string) $in);
        }

        yield from $this->resolveTransitiveDependencies($visited, $candidate);
        $in->addResolution($candidate);
    }

    protected function resolveSeek(array $visited, OfComponent $component, In $in): \Generator
    {
        foreach ($this->components as $other) {
            if ($other->offsetExists("CONTRIBUTE: " . $in->getName())) {
                // For CONTRIBUTEd, we just use all contributions.
                foreach ($other["CONTRIBUTE: " . $in->getName()] as $o) {
                    yield from $this->resolveTransitiveDependencies($visited, $o);
                    $in->addResolution($o);
                }
            }
        }
    }

    protected function resolveUse(array $visited, OfComponent $component, In $in): \Generator
    {
        $candidates = [];

        foreach ($this->components as $other) {
            if ($other->offsetExists("IMPLEMENT: " . $in->getName())) {
                // For IMPLEMENTed dependencies, we need to make choice.
                $candidates[] = $other["IMPLEMENT: " . $in->getName()];
            }
        }

        $candidates = array_merge(...$candidates);

        if (empty($candidates)) {
            throw new \LogicException("Could not resolve dependency for: " . (string) $in);
        }

        if (count($candidates) === 1) {
            yield from $this->resolveTransitiveDependencies($visited, $candidates[0]);
            $in->addResolution($candidates[0]);
            return;
        }

        $preferred_class = $this->disambiguateUse($component, $this->directives, $in);
        if (is_null($preferred_class)) {
            throw new \LogicException(
                "Dependency {$in->getName()} is provided (at least) twice, " .
                "no directives for {$component->getComponentName()}."
            );
        }
        foreach ($candidates as $candidate) {
            if ($candidate->aux["class"] === $preferred_class) {
                yield from $this->resolveTransitiveDependencies($visited, $candidates[0]);
                $in->addResolution($candidate);
                return;
            }
        }
        throw new \LogicException(
            "Dependency $preferred_class for service {$in->getName()} " .
            "for {$component->getComponentName()} could not be located."
        );
    }

    protected function resolveTransitiveDependencies(array $visited, Out $out): \Generator
    {
        $component = $out->getComponent();
        array_push($visited, [$component, $out]);
        foreach ($out->getDependencies() as $dep) {
            yield from $this->resolveDependency($visited, $component, $dep);
        }
        array_pop($visited);
    }

    protected function disambiguateUse(OfComponent $component, array $directives, In $in): ?string
    {
        foreach ($directives as $d) {
            if ($d instanceof RD\InComponent && $d->getComponentName() === $component->getComponentName()) {
                return $this->disambiguateUse($component, $d->getDirectives(), $in);
            }
            if ($d instanceof RD\ForXUseY && $d->getX() == (string) $in->getName()) {
                return $d->getY();
            }
        }
        return null;
    }
}
