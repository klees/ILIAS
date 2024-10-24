<?php declare(strict_types=1);

/* Copyright (c) 2017 Nils Haagen <nils.haagen@concepts-and-training.de> Extended GPL, see docs/LICENSE */

namespace ILIAS\UI\Implementation\Component\ViewControl;

use ILIAS\UI\Component as C;
use ILIAS\UI\Implementation\Component\ComponentHelper;
use ILIAS\UI\Implementation\Component\SignalGeneratorInterface;
use ILIAS\UI\Implementation\Component\Triggerer;
use ILIAS\UI\Implementation\Component\JavaScriptBindable;
use ILIAS\UI\Component\Signal;

class Sortation implements C\ViewControl\Sortation
{
    use ComponentHelper;
    use JavaScriptBindable;
    use Triggerer;

    /**
     * @var array<string,string>
     */
    protected array $options = array();

    protected Signal $select_signal;
    protected string $label = '';
    protected ?string $target_url = null;
    protected string $parameter_name = "sortation";
    protected ?string $active = null;
    protected SignalGeneratorInterface $signal_generator;

    public function __construct(array $options, SignalGeneratorInterface $signal_generator)
    {
        $this->options = $options;
        $this->signal_generator = $signal_generator;
        $this->initSignals();
    }

    /**
     * @inheritdoc
     */
    public function withResetSignals() : C\ViewControl\Sortation
    {
        $clone = clone $this;
        $clone->initSignals();
        return $clone;
    }

    /**
     * Set the signals for this component
     */
    protected function initSignals() : void
    {
        $this->select_signal = $this->signal_generator->create();
    }

    /**
     * @inheritdoc
     */
    public function withLabel(string $label) : C\ViewControl\Sortation
    {
        $clone = clone $this;
        $clone->label = $label;
        return $clone;
    }

    /**
     * @inheritdoc
     */
    public function getLabel() : string
    {
        return $this->label;
    }

    /**
     * @inheritdoc
     */
    public function withTargetURL($url, $parameter_name) : C\ViewControl\Sortation
    {
        $this->checkStringArg("url", $url);
        $this->checkStringArg("parameter_name", $parameter_name);
        $clone = clone $this;
        $clone->target_url = $url;
        $clone->parameter_name = $parameter_name;
        return $clone;
    }

    /**
     * @inheritdoc
     */
    public function getTargetURL() : ?string
    {
        return $this->target_url;
    }

    /**
     * @inheritdoc
     */
    public function getParameterName() : string
    {
        return $this->parameter_name;
    }

    /**
     * @inheritdoc
     */
    public function getOptions() : array
    {
        return $this->options;
    }

    /**
     * @inheritdoc
     */
    public function withOnSort(Signal $signal) : C\ViewControl\Sortation
    {
        return $this->withTriggeredSignal($signal, 'sort');
    }

    /**
     * @inheritdoc
     */
    public function getSelectSignal() : Signal
    {
        return $this->select_signal;
    }
}
