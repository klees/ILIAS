<?php declare(strict_types=1);

/* Copyright (c) 2017 Nils Haagen <nils.haagen@concepts-and-training.de> Extended GPL, see docs/LICENSE */

namespace ILIAS\UI\Component\ViewControl;

use \ILIAS\UI\Component\Component;
use \ILIAS\UI\Component\Signal;
use ILIAS\UI\Component\JavaScriptBindable;
use ILIAS\UI\Component\Triggerer;

/**
 * This describes a Sortation Control
 */
interface Sortation extends Component, JavaScriptBindable, Triggerer
{
    /**
     * Set the initial, non-functional entry
     */
    public function withLabel(string $label) : Sortation;

    /**
     * Get the label.
     */
    public function getLabel() : string;

    /**
     * Get a Sortation with this target-url.
     * Shy-Buttons in this control will link to this url
     * and add $parameter_name with the selected value.
     */
    public function withTargetURL(string $url, string $parameter_name) : Sortation;

    /**
     * Get the url this instance should trigger.
     */
    public function getTargetURL() : ?string;

    /**
     * Get the identifier of this instance.
     */
    public function getParameterName() : string;

    /**
     * Get the sorting-options.
     *
     * @return 	array<string,string> 	value=>title
     */
    public function getOptions() : array;

    /**
     * Get a component like this, triggering a signal of another component.
     *
     * @param Signal $signal A signal of another component
     */
    public function withOnSort(Signal $signal) : Sortation;

    /**
     * Get the Signal for the selection of a option
     */
    public function getSelectSignal() : Signal;
}
