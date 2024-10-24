<?php declare(strict_types=1);

/* Copyright (c) 2017 Richard Klees <richard.klees@concepts-and-training.de> Extended GPL, see docs/LICENSE */

namespace ILIAS\UI\Implementation\Component\Input\Field;

use ILIAS\UI\Component as C;
use ILIAS\Data\Factory as DataFactory;
use ILIAS\Refinery\Constraint;
use Closure;

/**
 * This implements the text input.
 */
class Text extends Input implements C\Input\Field\Text
{
    private ?int $max_length = null;
    private bool $complex = false;

    /**
     * @inheritdoc
     */
    public function __construct(
        DataFactory $data_factory,
        \ILIAS\Refinery\Factory $refinery,
        string $label,
        ?string $byline
    ) {
        parent::__construct($data_factory, $refinery, $label, $byline);
        $this->setAdditionalTransformation($refinery->custom()->transformation(function ($v) {
            return strip_tags($v);
        }));
    }

    /**
     * @inheritDoc
     */
    public function withMaxLength(int $max_length) : C\Input\Field\Input
    {
        $clone = $this->withAdditionalTransformation(
            $this->refinery->string()->hasMaxLength($max_length)
        );
        $clone->max_length = $max_length;

        return $clone;
    }

    /**
     * @inheritDoc
     */
    public function getMaxLength() : ?int
    {
        return $this->max_length;
    }

    /**
     * @inheritdoc
     */
    protected function isClientSideValueOk($value) : bool
    {
        if (!is_string($value)) {
            return false;
        }

        if ($this->max_length !== null &&
            strlen($value) > $this->max_length) {
            return false;
        }

        return true;
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
				il.UI.input.onFieldUpdate(event, '$id', $('#$id').val());
			});
			il.UI.input.onFieldUpdate(event, '$id', $('#$id').val());";
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
