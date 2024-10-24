<?php declare(strict_types=1);

/* Copyright (c) 2019 Nils Haagen <nils.haagen@concepts-and-training.de> Extended GPL, see docs/LICENSE */

namespace ILIAS\Refinery\To\Transformation;

use ILIAS\Refinery\DeriveApplyToFromTransform;
use ILIAS\Refinery\DeriveInvokeFromTransform;
use ILIAS\Refinery\Constraint;
use ILIAS\Refinery\ProblemBuilder;
use UnexpectedValueException;

/**
 * Transform a string representing a datetime-value to php's DateTimeImmutable
 * see https://www.php.net/manual/de/datetime.formats.php
 */
class DateTimeTransformation implements Constraint
{
    use DeriveApplyToFromTransform;
    use DeriveInvokeFromTransform;
    use ProblemBuilder;

    protected string $error = '';

    /**
     * @inheritdoc
     */
    public function transform($from)
    {
        $this->check($from);
        return new \DateTimeImmutable($from);
    }

    public function getError()
    {
        return $this->error;
    }

    public function check($value)
    {
        if (!$this->accepts($value)) {
            throw new UnexpectedValueException($this->getErrorMessage($value));
        }

        return null;
    }

    public function accepts($value) : bool
    {
        try {
            new \DateTimeImmutable($value);
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            return false;
        }
        return true;
    }

    public function problemWith($value) : ?string
    {
        if (!$this->accepts($value)) {
            return $this->getErrorMessage($value);
        }

        return null;
    }
}
