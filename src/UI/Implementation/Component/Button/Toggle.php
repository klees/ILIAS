<?php declare(strict_types=1);

/* Copyright (c) 2018 Thomas Famula <famula@leifos.de> Extended GPL, see docs/LICENSE */

namespace ILIAS\UI\Implementation\Component\Button;

use ILIAS\UI\Component as C;
use ILIAS\UI\Implementation\Component\ComponentHelper;
use ILIAS\UI\Implementation\Component\JavaScriptBindable;
use ILIAS\UI\Implementation\Component\Triggerer;
use ILIAS\UI\Component\Signal;

class Toggle extends Button implements C\Button\Toggle
{
    use ComponentHelper;
    use JavaScriptBindable;
    use Triggerer;

    protected ?string $action_off = null;
    protected ?string $action_on = null;

    public function __construct(string $label, $action_on, $action_off, bool $is_on, Signal $click = null)
    {
        $this->checkStringOrSignalArg("action", $action_on);
        $this->checkStringOrSignalArg("action_off", $action_off);

        // no way to resolve conflicting string actions
        $button_action = (is_null($click)) ? "" : $click;

        parent::__construct($label, $button_action);

        if (is_string($action_on)) {
            $this->action_on = $action_on;
        } else {
            $this->setTriggeredSignal($action_on, "toggle_on");
        }

        if (is_string($action_off)) {
            $this->action_off = $action_off;
        } else {
            $this->setTriggeredSignal($action_off, "toggle_off");
        }

        $this->is_engageable = true;
        $this->engaged = $is_on;
    }

    /**
     * @inheritdoc
     */
    public function getActionOff()
    {
        if ($this->action_off !== null) {
            return $this->action_off;
        }

        return $this->getTriggeredSignalsFor("toggle_off");
    }

    /**
     * @inheritdoc
     */
    public function getActionOn()
    {
        if ($this->action_on !== null) {
            return $this->action_on;
        }

        return $this->getTriggeredSignalsFor("toggle_on");
    }

    public function withAdditionalToggleOnSignal(Signal $signal) : C\Button\Toggle
    {
        return $this->appendTriggeredSignal($signal, "toggle_on");
    }

    public function withAdditionalToggleOffSignal(Signal $signal) : C\Button\Toggle
    {
        return $this->appendTriggeredSignal($signal, "toggle_off");
    }
}
