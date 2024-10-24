<?php declare(strict_types=1);

/* Copyright (c) 2017 Jesús López <lopez@leifos.com> Extended GPL, see docs/LICENSE */

namespace ILIAS\UI\Implementation\Component\Input\Field;

use ILIAS\Data\Factory as DataFactory;
use ILIAS\UI\Component as C;
use ILIAS\Refinery\Constraint;
use Closure;

/**
 * This implements the select.
 */
class Select extends Input implements C\Input\Field\Select
{
    protected array $options;
    protected string $label;

    /**
     * @var mixed
     */
    protected $value;
    private bool $complex = false;

    public function __construct(
        DataFactory $data_factory,
        \ILIAS\Refinery\Factory $refinery,
        string $label,
        array $options,
        ?string $byline
    ) {
        parent::__construct($data_factory, $refinery, $label, $byline);
        $this->options = $options;
    }

    /**
     * @return array with the key/value options.
     */
    public function getOptions() : array
    {
        return $this->options;
    }

    /**
     * @inheritdoc
     */
    protected function isClientSideValueOk($value) : bool
    {
        return in_array($value, array_keys($this->options)) || $value == "";
    }

    /**
     * @inheritdoc
     */
    protected function getConstraintForRequirement() : ?Constraint
    {
        return $this->refinery->string()->hasMinLength(1);
    }

    /**
     * @inheritdoc
     */
    public function getUpdateOnLoadCode() : Closure
    {
        return function ($id) {
            return "$('#$id').on('input', function(event) {
				il.UI.input.onFieldUpdate(event, '$id', $('#$id option:selected').text());
			});
			il.UI.input.onFieldUpdate(event, '$id', $('#$id option:selected').text());";
        };
    }

    /**
     * @inheritdoc
     */
    public function isComplex() : bool
    {
        return $this->complex;
    }
}
