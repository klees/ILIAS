<?php declare(strict_types=1);
/* Copyright (c) 1998-2018 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * Interface ilTermsOfServiceLogicalAndDocumentCriteriaEvaluation
 * @author Michael Jansen <mjansen@databay.de>
 */
class ilTermsOfServiceLogicalAndDocumentCriteriaEvaluation implements ilTermsOfServiceDocumentCriteriaEvaluation
{
    protected ilTermsOfServiceCriterionTypeFactoryInterface $criterionTypeFactory;
    protected ilObjUser $user;
    protected ilLogger $log;

    public function __construct(
        ilTermsOfServiceCriterionTypeFactoryInterface $criterionTypeFactory,
        ilObjUser $user,
        ilLogger $log
    ) {
        $this->criterionTypeFactory = $criterionTypeFactory;
        $this->user = $user;
        $this->log = $log;
    }

    public function withContextUser(ilObjUser $user) : ilTermsOfServiceDocumentCriteriaEvaluation
    {
        $clone = clone $this;
        $clone->user = $user;

        return $clone;
    }

    public function evaluate(ilTermsOfServiceSignableDocument $document) : bool
    {
        $this->log->debug(sprintf(
            'Evaluating criteria for document "%s" (id: %s) and user "%s" (id: %s)',
            $document->title(),
            $document->id(),
            $this->user->getLogin(),
            $this->user->getId()
        ));

        foreach ($document->criteria() as $criterionAssignment) {
            $criterionType = $this->criterionTypeFactory->findByTypeIdent($criterionAssignment->getCriterionId(), true);

            $result = $criterionType->evaluate($this->user, $criterionAssignment->getCriterionValue());

            $this->log->debug(sprintf(
                'Criterion of type "%s", configured with %s evaluated: %s',
                $criterionType->getTypeIdent(),
                var_export($criterionAssignment->getCriterionValue()->toJson(), true),
                var_export($result, true)
            ));

            if (!$result) {
                return false;
            }
        }

        return true;
    }
}
