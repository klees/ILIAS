<?php declare(strict_types=1);
/* Copyright (c) 1998-2018 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * Interface ilTermsOfServiceEvaluableCriterion
 * @author Michael Jansen <mjansen@databay.de>
 */
interface ilTermsOfServiceEvaluableCriterion
{
    public function getCriterionValue() : ilTermsOfServiceCriterionConfig;

    public function getCriterionId() : string;
}
