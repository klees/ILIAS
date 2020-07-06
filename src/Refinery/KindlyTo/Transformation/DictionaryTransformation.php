<?php
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

    }



    public function __invoke($from)
    {
        return $this->transform($from);
    }
}