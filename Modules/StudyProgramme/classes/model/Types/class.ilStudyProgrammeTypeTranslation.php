<?php declare(strict_types=1);

/**
 * Class ilStudyProgrammeTypeTranslation
 * This class represents a translation for a given ilStudyProgrammeType object and language.
 *
 * @author: Michael Herren <mh@studer-raimann.ch>
 */
class ilStudyProgrammeTypeTranslation
{
    protected int $id;
    protected int $prg_type_id = 0;
    protected string $lang = '';
    protected string $member = '';
    protected string $value = '';

    public function __construct(int $id)
    {
        $this->id = $id;
    }

    public function getId() : int
    {
        return $this->id;
    }

    public function setId(int $id) : void
    {
        $this->id = $id;
    }

    public function getPrgTypeId() : int
    {
        return $this->prg_type_id;
    }

    public function setPrgTypeId(int $prg_type_id) : void
    {
        $this->prg_type_id = $prg_type_id;
    }

    public function getLang() : string
    {
        return $this->lang;
    }

    public function setLang(string $lang) : void
    {
        $this->lang = $lang;
    }

    public function getMember() : string
    {
        return $this->member;
    }

    public function setMember(string $member) : void
    {
        $this->member = $member;
    }

    public function getValue() : string
    {
        return $this->value;
    }

    public function setValue(string $value) : void
    {
        $this->value = $value;
    }
}
