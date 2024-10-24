<?php declare(strict_types=1);
/* Copyright (c) 1998-2018 ILIAS open source, Extended GPL, see docs/LICENSE */

use ILIAS\Data\Factory;
use ILIAS\Refinery\Custom\Constraint;

/**
 * Class ilTermsOfServiceDocumentCriterionAssignmentConstraint
 * @author Michael Jansen <mjansen@databay.de>
 */
class ilTermsOfServiceDocumentCriterionAssignmentConstraint extends Constraint
{
    protected ilTermsOfServiceCriterionTypeFactoryInterface $criterionTypeFactory;
    protected ilTermsOfServiceDocument $document;

    public function __construct(
        ilTermsOfServiceCriterionTypeFactoryInterface $criterionTypeFactory,
        ilTermsOfServiceDocument $document,
        Factory $dataFactory,
        ilLanguage $lng
    ) {
        $this->criterionTypeFactory = $criterionTypeFactory;
        $this->document = $document;

        parent::__construct(
            function (ilTermsOfServiceDocumentCriterionAssignment $value) : bool {
                return 0 === count($this->filterEqualValues($value));
            },
            static function ($txt, $value) : string {
                return 'The passed assignment must be unique for the document!';
            },
            $dataFactory,
            $lng
        );
    }

    /**
     * @param ilTermsOfServiceDocumentCriterionAssignment $value
     * @return ilTermsOfServiceDocumentCriterionAssignment[]|ilTermsOfServiceEvaluableCriterion[]
     */
    protected function filterEqualValues(
        ilTermsOfServiceDocumentCriterionAssignment $value
    ) : array {
        $otherValues = $this->document->criteria();

        return array_filter(
            $otherValues,
            function (ilTermsOfServiceDocumentCriterionAssignment $otherValue) use ($value) : bool {
                $idCurrent = $otherValue->getId();
                $idNew = $value->getId();

                $uniqueIdEquals = $idCurrent === $idNew;
                if ($uniqueIdEquals) {
                    return false;
                }

                $valuesEqual = $value->equals($otherValue);
                if ($valuesEqual) {
                    return true;
                }

                return $this->haveSameNature($value, $otherValue);
            }
        );
    }

    /**
     * @param ilTermsOfServiceDocumentCriterionAssignment $value
     * @param ilTermsOfServiceDocumentCriterionAssignment $otherValue
     * @return bool
     * @throws ilTermsOfServiceCriterionTypeNotFoundException
     */
    protected function haveSameNature(
        ilTermsOfServiceDocumentCriterionAssignment $value,
        ilTermsOfServiceDocumentCriterionAssignment $otherValue
    ) : bool {
        if ($value->getCriterionId() !== $otherValue->getCriterionId()) {
            return false;
        }

        $valuesHaveSameNature = $this->criterionTypeFactory->findByTypeIdent($value->getCriterionId())->hasUniqueNature();

        return $valuesHaveSameNature;
    }
}
