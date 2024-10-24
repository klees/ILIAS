<?php declare(strict_types=1);
/* Copyright (c) 1998-2013 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * AMD field type date time
 * @author  Jörg Lützenkirchen <luetzenkirchen@leifos.com>
 * @version $Id$
 * @ingroup ServicesAdvancedMetaData
 */
class ilAdvancedMDFieldDefinitionDateTime extends ilAdvancedMDFieldDefinition
{
    //
    // generic types
    //

    public function getType() : int
    {
        return self::TYPE_DATETIME;
    }


    //
    // ADT
    //

    protected function initADTDefinition() : ilADTDefinition
    {
        return ilADTFactory::getInstance()->getDefinitionInstanceByType("DateTime");
    }


    //
    // import/export
    //

    public function getValueForXML(ilADT $element) : string
    {
        return $element->getDate()->get(IL_CAL_DATETIME);
    }

    public function importValueFromXML(string $a_cdata) : void
    {
        $this->getADT()->setDate(new ilDate($a_cdata, IL_CAL_DATETIME));
    }

    public function importFromECS(string $a_ecs_type, $a_value, string $a_sub_id) : bool
    {
        $value = '';
        switch ($a_ecs_type) {
            case ilECSUtils::TYPE_TIMEPLACE:
                if ($a_value instanceof ilECSTimePlace) {
                    $value = new ilDateTime($a_value->{'getUT' . ucfirst($a_sub_id)}(), IL_CAL_UNIX);
                }
                break;
        }

        if ($value instanceof ilDateTime) {
            $this->getADT()->setDate($value);
            return true;
        }
        return false;
    }

    /**
     * @inheritdoc
     */
    public function getLuceneSearchString($a_value) : string
    {
        // see ilADTDateTimeSearchBridgeRange::importFromPost();

        if ($a_value["tgl"]) {
            $start = mktime(
                $a_value["lower"]["time"]["h"],
                $a_value["lower"]["time"]["m"],
                1,
                $a_value["lower"]["date"]["m"],
                $a_value["lower"]["date"]["d"],
                $a_value["lower"]["date"]["y"]
            );

            $end = mktime(
                $a_value["upper"]["time"]["h"],
                $a_value["upper"]["time"]["m"],
                1,
                $a_value["upper"]["date"]["m"],
                $a_value["upper"]["date"]["d"],
                $a_value["upper"]["date"]["y"]
            );

            if ($start && $end && $start > $end) {
                $tmp = $start;
                $start = $end;
                $end = $tmp;
            }

            $start = new ilDateTime($start, IL_CAL_UNIX);
            $end = new ilDateTime($end, IL_CAL_UNIX);

            return "{" . $start->get(IL_CAL_DATETIME) . " TO " . $end->get(IL_CAL_DATETIME) . "}";
        }
        return 'null';
    }
}
