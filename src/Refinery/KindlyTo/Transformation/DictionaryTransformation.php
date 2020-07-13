<?php
declare(strict_types=1);
/* Copyright (c) 1998-2019 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * @author Luka Stocker <lstocker@concepts-and-training.de>
 */

namespace ILIAS\Refinery\KindlyTo\Transformation;

use ILIAS\Refinery\DeriveApplyToFromTransform;
use ILIAS\Refinery\Transformation;
use ILIAS\Refinery\ConstraintViolationException;

class DictionaryTransformation implements Transformation
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

        $result = array();
        foreach($from as $key => $value)
        {
            if(false === is_string($key))
            {
                throw new ConstraintViolationException(
                    'Key is not a string',
                    'key_is_no_string'
                );
            }
            $transformedValue = $this->transformation->transform($value);
            $result[$key] = $transformedValue;
        }
        printf($result);
        return $result;
    }

    /**
     * @inheritDoc
     */
    public function __invoke($from)
    {
        return $this->transform($from);
    }
}