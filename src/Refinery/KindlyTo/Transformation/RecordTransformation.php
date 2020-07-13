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

class RecordTransformation implements Transformation
{
    use DeriveApplyToFromTransform;

    /**
     *@var Transformation[]
     */
    private $transformations;

    /**
     *@param Transformation[] $transformations
     */

    public function __construct(array $transformations)
    {

    }

    public function transform($form)
    {

    }

    public function __invoke($from)
    {
        // TODO: Implement __invoke() method.
    }
}