<?php declare(strict_types=1);
/* Copyright (c) 1998-2018 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * Class ilTermsOfServiceDocument
 * @author Michael Jansen <mjansen@databay.de>
 */
class ilTermsOfServiceDocument extends ActiveRecord implements ilTermsOfServiceSignableDocument
{
    private const TABLE_NAME = 'tos_documents';

    /**
     * @var int
     * @db_has_field        true
     * @db_fieldtype        integer
     * @db_length           4
     * @db_is_primary       true
     * @con_sequence        true
     */
    protected ?int $id = null;

    /**
     * @var int
     * @con_has_field   true
     * @con_fieldtype   integer
     * @con_length      4
     */
    protected int $creation_ts = 0;

    /**
     * @var int
     * @con_has_field   true
     * @con_fieldtype   integer
     * @con_length      4
     */
    protected int $modification_ts = 0;

    /**
     * @var int
     * @con_has_field   true
     * @con_fieldtype   integer
     * @con_length      4
     */
    protected int $owner_usr_id = 0;

    /**
     * @var int
     * @con_has_field   true
     * @con_fieldtype   integer
     * @con_length      4
     */
    protected int $last_modified_usr_id = 0;

    /**
     * @var int
     * @con_has_field   true
     * @con_fieldtype   integer
     * @con_length      4
     */
    protected ?int $sorting = 0;

    /**
     * @var string
     * @db_has_field        true
     * @db_fieldtype        text
     * @db_length           255
     */
    protected ?string $title = null;

    /**
     * @var string
     * @db_has_field        true
     * @db_fieldtype        clob
     */
    protected string $text = '';

    /** @var ilTermsOfServiceDocumentCriterionAssignment[] */
    protected array $criteria = [];

    /** @var ilTermsOfServiceDocumentCriterionAssignment[] */
    protected array $initialPersistedCriteria = [];

    private bool $criteriaFetched = false;

    public static function returnDbTableName() : string
    {
        return self::TABLE_NAME;
    }

    public function content() : string
    {
        return $this->text;
    }

    public function title() : string
    {
        return $this->title;
    }

    public function id() : int
    {
        return $this->id;
    }

    public function read() : void
    {
        parent::read();

        $this->fetchAllCriterionAssignments();
    }

    public function buildFromArray(array $array) : ActiveRecord
    {
        $document = parent::buildFromArray($array);

        $this->fetchAllCriterionAssignments();

        return $document;
    }

    public function create() : void
    {
        $this->setCreationTs(time());

        parent::create();

        foreach ($this->criteria as $criterionAssignment) {
            $criterionAssignment->setDocId($this->getId());
            $criterionAssignment->store();
        }

        $this->initialPersistedCriteria = $this->criteria;
    }

    public function update() : void
    {
        $this->setModificationTs(time());

        foreach ($this->criteria as $criterionAssignment) {
            $criterionAssignment->setDocId($this->getId());
            $criterionAssignment->store();
        }

        foreach ($this->initialPersistedCriteria as $criterionAssignment) {
            $found = array_filter(
                $this->criteria,
                static function (ilTermsOfServiceDocumentCriterionAssignment $criterionToMatch) use (
                    $criterionAssignment
                ) : bool {
                    return $criterionToMatch->getId() === $criterionAssignment->getId();
                }
            );

            if (0 === count($found)) {
                $criterionAssignment->delete();
            }
        }

        $this->initialPersistedCriteria = $this->criteria;

        parent::update();
    }

    public function delete() : void
    {
        foreach ($this->initialPersistedCriteria as $criterionAssignment) {
            $criterionAssignment->delete();
        }

        $this->initialPersistedCriteria = $this->criteria = [];

        parent::delete();
    }

    public function criteria() : array
    {
        return $this->criteria;
    }

    /**
     * @param ilTermsOfServiceDocumentCriterionAssignment $criterionAssignment
     * @throws ilTermsOfServiceDuplicateCriterionAssignmentException
     */
    public function attachCriterion(ilTermsOfServiceDocumentCriterionAssignment $criterionAssignment) : void
    {
        foreach ($this->criteria as $currentAssignment) {
            if ($currentAssignment->equals($criterionAssignment)) {
                throw new ilTermsOfServiceDuplicateCriterionAssignmentException(sprintf(
                    'Cannot attach duplicate criterion with criterion typeIdent %s and value: %s',
                    $criterionAssignment->getCriterionId(),
                    var_export($criterionAssignment->getCriterionValue(), true)
                ));
            }
        }

        $this->criteria[] = $criterionAssignment;
    }

    /**
     * @param ilTermsOfServiceDocumentCriterionAssignment
     * @throws OutOfBoundsException
     */
    public function detachCriterion(ilTermsOfServiceDocumentCriterionAssignment $criterionAssignment) : void
    {
        $numCriteriaBeforeRemoval = count($this->criteria);

        $this->criteria = array_filter(
            $this->criteria,
            static function (ilTermsOfServiceDocumentCriterionAssignment $currentAssignment) use (
                $criterionAssignment
            ) : bool {
                return !$currentAssignment->equals($criterionAssignment);
            }
        );

        $numCriteriaAfterRemoval = count($this->criteria);

        if ($numCriteriaAfterRemoval === $numCriteriaBeforeRemoval) {
            throw new OutOfBoundsException(sprintf(
                'Could not find any criterion with criterion typeIdent %s and value: %s',
                $criterionAssignment->getCriterionId(),
                var_export($criterionAssignment->getCriterionValue(), true)
            ));
        }
    }

    public function fetchAllCriterionAssignments() : void
    {
        if (!$this->criteriaFetched) {
            $this->criteriaFetched = true;

            $this->initialPersistedCriteria = [];
            $this->criteria = [];

            $criteria = ilTermsOfServiceDocumentCriterionAssignment::where(['doc_id' => $this->getId()])->get();
            foreach ($criteria as $criterionAssignment) {
                /** @var ilTermsOfServiceDocumentCriterionAssignment $criterionAssignment */
                $this->criteria[] = $criterionAssignment;
            }

            $this->initialPersistedCriteria = $this->criteria;
        }
    }
}
