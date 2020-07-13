<?php
declare(strict_types=1);
/* Copyright (c) 1998-2020 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * @author Luka Stocker <lstocker@concepts-and-training.de>
 */

namespace ILIAS\Refinery\KindlyTo\Transformation;

use ILIAS\Refinery\DeriveApplyToFromTransform;
use ILIAS\Refinery\Transformation;
use ILIAS\Refinery\ConstraintViolationException;

class TupleTransformation implements Transformation
{
    use DeriveApplyToFromTransform;

    /**
     * @var Transformation[]
     */
    private $transformations;

    /**
     * @param array $transformations;
     */
    public function __construct()
    {
    }

    /**
     * @inheritDoc
     */
    public function transform($from)
    {
        // TODO: Implement transform() method.
    }

    /**
     * @inheritDoc
     */
    public function __invoke($from)
    {
        return $this->transform($from);
    }
}