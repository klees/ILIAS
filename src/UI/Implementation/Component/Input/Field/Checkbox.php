<?php declare(strict_types=1);

/* Copyright (c) 2017 Timon Amstutz <timon.amstutz@ilub.unibe.ch> Extended GPL, see docs/LICENSE */

namespace ILIAS\UI\Implementation\Component\Input\Field;

use ILIAS\UI\Component as C;
use ILIAS\UI\Implementation\Component\JavaScriptBindable;
use ILIAS\UI\Implementation\Component\Triggerer;
use ILIAS\UI\Implementation\Component\Input\InputData;
use ILIAS\Refinery\Constraint;
use Closure;
use LogicException;
use InvalidArgumentException;

/**
 * This implements the checkbox input.
 */
class Checkbox extends Input implements C\Input\Field\Checkbox, C\Changeable, C\Onloadable
{
    use JavaScriptBindable;

    /**
     * @inheritdoc
     */
    protected function getConstraintForRequirement() : ?Constraint
    {
        return null;
    }

    /**
     * @inheritdoc
     */
    protected function isClientSideValueOk($value) : bool
    {
        if ($value == "checked" || $value === "" || is_bool($value)) {
            return true;
        } else {
            return false;
        }
    }


    /**
     * @inheritdoc
     */
    public function withValue($value) : C\Input\Field\Input
    {
        if (!is_bool($value)) {
            throw new InvalidArgumentException(
                "Unknown value type for checkbox: " . gettype($value)
            );
        }

        return parent::withValue($value);
    }


    /**
     * @inheritdoc
     */
    public function withInput(InputData $input) : C\Input\Field\Input
    {
        if ($this->getName() === null) {
            throw new LogicException("Can only collect if input has a name.");
        }

        if (!$this->isDisabled()) {
            $value = $input->getOr($this->getName(), "");
            $clone = $this->withValue($value === "checked");
        } else {
            $clone = $this;
        }

        $clone->content = $this->applyOperationsTo($clone->getValue());
        if ($clone->content->isError()) {
            return $clone->withError("" . $clone->content->error());
        }

        return $clone;
    }

    /**
     * @inheritdoc
     */
    public function appendOnLoad(C\Signal $signal) : C\Onloadable
    {
        return $this->appendTriggeredSignal($signal, 'load');
    }

    /**
     * @inheritdoc
     */
    public function withOnChange(C\Signal $signal) : C\Changeable
    {
        return $this->withTriggeredSignal($signal, 'change');
    }

    /**
     * @inheritdoc
     */
    public function appendOnChange(C\Signal $signal) : C\Changeable
    {
        return $this->appendTriggeredSignal($signal, 'change');
    }

    /**
     * @inheritdoc
     */
    public function withOnLoad(C\Signal $signal) : C\Onloadable
    {
        return $this->withTriggeredSignal($signal, 'load');
    }

    /**
     * @inheritdoc
     */
    public function getUpdateOnLoadCode() : Closure
    {
        return function ($id) {
            return "$('#$id').on('input', function(event) {
			    il.UI.input.onFieldUpdate(event, '$id', $('#$id').prop('checked').toString());
		    });
		    il.UI.input.onFieldUpdate(event, '$id', $('#$id').prop('checked').toString());";
        };
    }
}
