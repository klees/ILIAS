<?php
declare(strict_types=1);
/* Copyright (c) 1998-2020 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * @author  Luka Stocker <lstocker@concepts-and-training.de>
 */

namespace ILIAS\Refinery\KindlyTo\Transformation;

use ILIAS\Data\Result;
use ILIAS\Refinery\ConstraintViolationException;
use ILIAS\Refinery\DeriveApplyToFromTransform;
use ILIAS\Refinery\Transformation;

class ListTransformation implements Transformation
{
    use DeriveApplyToFromTransform;
    /**
     * @var Transformation
     */
    private $transformation;

    /**
     * @param Transformation $transformation
     */
    public function __construct(Transformation $transformation)
    {
        $this->transformation = $transformation;
    }

    /**
     * @inheritdoc
     */
    public function transform($from)
    {
        if(false == is_array($from))
        {
            $from = array($from);
            if(array_values() === $from)
            {
               throw new ConstraintViolationException(
                   'The array ist empty',
                   'value_array_is_empty'
               ) ;
            }
        }

        if(array_values() === $from)
        {
            throw new ConstraintViolationException(
            'The array ist empty',
            'value_array_is_empty'
            ) ;
        }

        $result = array();
        foreach($from as $val)
        {
            $transformedVal = $this->transformation->transform($val);
            $result[] = $transformedVal;
        }
        return $result;
    }

    public function applyTo(Result $data): Result
    {
    }
    /**
     * @inheritdoc
     */
    public function __invoke($from)
    {
        return $this->transform($from);
    }
}