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

use ILIAS\Component\Component;

class OfComponent implements \ArrayAccess
{
    protected Component $component;
    protected array $dependencies = [];
    protected array $resolutions = [];

    public function __construct(Component $component, Dependency ...$ds)
    {
        $this->component = $component;

        foreach ($ds as $d) {
            if (!isset($this->dependencies[(string) $d])) {
                $this->dependencies[(string) $d] = [];
            }
            $this->dependencies[(string) $d][] = $d;
            if ($d instanceof Out) {
                $d->setComponent($this);
            }
        }
    }

    public function getComponent(): Component
    {
        return $this->component;
    }

    public function getComponentName(): string
    {
        return get_class($this->getComponent());
    }

    public function getInDependencies(): \Iterator
    {
        foreach ($this->dependencies as $d) {
            foreach ($d as $i) {
                if ($i instanceof In) {
                    yield $i;
                }
            }
        }
    }

    public function getInDependenciesOf(InType $type): \Iterator
    {
        foreach ($this->dependencies as $d) {
            foreach ($d as $i) {
                if ($i instanceof In && $i->getType() === $type) {
                    yield $i;
                }
            }
        }
    }

    public function getOutDependenciesOf(OutType $type): \Iterator
    {
        foreach ($this->dependencies as $d) {
            foreach ($d as $o) {
                if ($o instanceof Out && $o->getType() === $type) {
                    yield $o;
                }
            }
        }
    }

    public function addResolution(In $in, array|Out $other)
    {
        if (!isset($this[(string) $in])) {
            throw new \LogicException("Can't add resolution for unknown dependency.");
        }
        if ($in->getType() !== InType::SEEK) {
            if (isset($this->resolution)) {
                throw new \LogicException(
                    "Dependency of type {$in->getType()->value} can only be resolved once."
                );
            }
            if (!($other instanceof Out)) {
                throw new \LogicException(
                    "Dependency of type {$in->getType()->value} can only be resolved by plain Out."
                );
            }
            $this->resolutions[(string) $in] = $other;
        } else {
            if (!isset($this->resolution)) {
                $this->resolutions[(string) $in] = [];
            }
            if (!is_array($other)) {
                throw new \LogicException(
                    "Dependency of type {$in->getType()->value} can only be resolved by array of Outs."
                );
            }
            $this->resolutions[(string) $in] = $other;
        }
    }

    public function getResolution(In $in): array|Out
    {
        if (!isset($this->resolutions[(string) $in])) {
            throw new \LogicException(
                "No resolution for {$in}..."
            );
        }
        return $this->resolutions[(string) $in];
    }

    public function isResolved(In $in): bool
    {
        return isset($this->resolutions[(string) $in]);
    }

    // ArrayAccess

    public function offsetExists($dependency_description): bool
    {
        return array_key_exists($dependency_description, $this->dependencies);
    }

    public function offsetGet($dependency_description): ?array
    {
        return $this->dependencies[$dependency_description];
    }

    public function offsetSet($offset, $value): void
    {
        throw new \LogicException(
            "Cannot modify dependencies of component."
        );
    }

    public function offsetUnset($offset): void
    {
        throw new \LogicException(
            "Cannot modify dependencies of component."
        );
    }
}
