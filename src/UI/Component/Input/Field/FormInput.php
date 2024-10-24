<?php declare(strict_types=1);

/* Copyright (c) 2017 Richard Klees <richard.klees@concepts-and-training.de> Extended GPL, see docs/LICENSE */

namespace ILIAS\UI\Component\Input\Field;

use ILIAS\UI\Component\JavaScriptBindable;
use ILIAS\UI\Component\OnUpdateable;
use Closure;

/**
 * This describes inputs that can be used in forms.
 */
interface FormInput extends Input, JavaScriptBindable, OnUpdateable
{
    /**
     * Get the label of the input.
     */
    public function getLabel() : string;

    /**
     * Get an input like this, but with a replaced label.
     *
     * @return static
     */
    public function withLabel(string $label);

    /**
     * Get the byline of the input.
     */
    public function getByline() : ?string;

    /**
     * Get an input like this, but with an additional/replaced label.
     *
     * @return static
     */
    public function withByline(string $byline);

    /**
     * Is this field required?
     */
    public function isRequired() : bool;

    /**
     * Get an input like this, but set the field to be required (or not).
     *
     * @return static
     */
    public function withRequired(bool $is_required);

    /**
     * Is this input disabled?
     */
    public function isDisabled() : bool;

    /**
     * Get an input like this, but set it to a disabled state.
     *
     * @return static
     */
    public function withDisabled(bool $is_disabled);

    /**
     * The error of the input as used in HTML.
     */
    public function getError() : ?string;

    /**
     * Get an input like this one, with a different error.
     *
     * @return static
     */
    public function withError(string $error);

    /**
     * Get update code
     *
     * This method has to return JS code that calls
     * il.UI.filter.onFieldUpdate(event, '$id', string_value);
     * - initially "onload" and
     * - on every input change.
     * It must pass a readable string representation of its value in parameter 'string_value'.
     */
    public function getUpdateOnLoadCode() : Closure;
}
