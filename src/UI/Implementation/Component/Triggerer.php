<?php declare(strict_types=1);

namespace ILIAS\UI\Implementation\Component;

use ILIAS\UI\Component as C;

/**
 * Trait Triggerer
 *
 * Provides helper methods and default implementation for components acting as triggerer
 *
 * TODO: This is missing tests!
 *
 * @author Stefan Wanzenried <sw@studer-raimann.ch>
 * @package ILIAS\UI\Implementation\Component
 */
trait Triggerer
{
    /**
     * @var TriggeredSignal[]
     */
    private array $triggered_signals = array();

    /**
     * Append a triggered signal to other signals of the same event
     *
     * @return static
     */
    protected function appendTriggeredSignal(C\Signal $signal, string $event)
    {
        $clone = clone $this;
        if (!isset($clone->triggered_signals[$event])) {
            $clone->triggered_signals[$event] = array();
        }
        $clone->triggered_signals[$event][] = new TriggeredSignal($signal, $event);
        return $clone;
    }

    /**
     * Add a triggered signal, replacing any other signals registered on the same event
     *
     * @return static
     */
    protected function withTriggeredSignal(C\Signal $signal, string $event)
    {
        $clone = clone $this;
        $clone->setTriggeredSignal($signal, $event);
        return $clone;
    }

    /**
     * Add a triggered signal, replacing any other signals registered on the same event.
     *
     * ATTENTION: This mutates the original object and should only be used when there
     * is no other possibility.
     */
    protected function setTriggeredSignal(C\Signal $signal, string $event) : void
    {
        $this->triggered_signals[$event] = array();
        $this->triggered_signals[$event][] = new TriggeredSignal($signal, $event);
    }

    /**
     * @return TriggeredSignal[]
     */
    public function getTriggeredSignals() : array
    {
        return $this->flattenArray($this->triggered_signals);
    }

    /**
     * Get signals that are triggered for a certain event.
     *
     * @return C\Signal[]
     */
    public function getTriggeredSignalsFor(string $event) : array
    {
        if (!isset($this->triggered_signals[$event])) {
            return [];
        }
        return array_map(
            function ($ts) {
                return $ts->getSignal();
            },
            $this->triggered_signals[$event]
        );
    }

    public function withResetTriggeredSignals() : C\Triggerer
    {
        $clone = clone $this;
        $clone->triggered_signals = array();
        return $clone;
    }

    /**
     * Flatten a multidimensional array to a single dimension
     */
    private function flattenArray(array $array) : array
    {
        $flatten = array();
        array_walk_recursive($array, function ($a) use (&$flatten) {
            $flatten[] = $a;
        });
        return $flatten;
    }
}
