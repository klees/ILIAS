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
    public function __construct(array $transformations)
    {
        $this->transformations = $transformations;
    }

    /**
     * @inheritDoc
     */
    public function transform($from)
    {
        if(false == is_array($from))
        {
            $from = array($from);
            if(array() === $from)
            {
                throw new ConstraintViolationException(
                    'The array ist empty',
                    'value_array_is_empty'
                ) ;
            }
        }
        elseif(array() === $from)
        {
            throw new ConstraintViolationException(
                'The array ist empty',
                'value_array_is_empty'
            ) ;
        }

        $this->ValueLength($from);
        $result = array();
        foreach($from as $key => $value)
        {
            if(false === array_key_exists($key, $this->transformations))
            {
                throw new ConstraintViolationException(
                    'Matching values not found',
                    'matching_values_not_found'
                );
            }
            $transformedValue = $this->transformations[$key]->transform($value);
            $result[] = $transformedValue;
        }
        return $result;

    }

    /**
     * @param $values
     */
    public function ValueLength($values)
    {
        $countValues = count($values);
        $countTransformations = count($this->transformations);

        if ($countValues !== $countTransformations) {
            throw new ConstraintViolationException(

                'The given values does not match',
                'value_length_does_not_match'

            );
        }
    }

    /**
     * @inheritDoc
     */
    public function __invoke($from)
    {
        return $this->transform($from);
    }
}