<?php declare(strict_types=1);

/******************************************************************************
 *
 * This file is part of ILIAS, a powerful learning management system.
 *
 * ILIAS is licensed with the GPL-3.0, you should have received a copy
 * of said license along with the source code.
 *
 * If this is not the case or you just want to try ILIAS, you'll find
 * us at:
 *      https://www.ilias.de
 *      https://github.com/ILIAS-eLearning
 *
 *****************************************************************************/
/**
 * Class ilCmiXapiDateTimeDurationInputGUI
 *
 * @author      Uwe Kohnle <kohnle@internetlehrer-gmbh.de>
 * @author      Björn Heyser <info@bjoernheyser.de>
 * @author      Stefan Schneider <info@eqsoft.de>
 *
 * @package     Module/CmiXapi
 */
class ilCmiXapiDateDurationInputGUI extends ilDateDurationInputGUI
{
    public function getValue() : array
    {
        $duration = array();
        
        if ($this->getStart() instanceof ilDateTime) {
            $duration['start'] = $this->getStart()->get(IL_CAL_UNIX);
        }
        
        if ($this->getEnd() instanceof ilDateTime) {
            $duration['end'] = $this->getEnd()->get(IL_CAL_UNIX);
        }
        
        return $duration;
    }
    
    /**
     * @return ilCmiXapiDateTime|null
     */
    public function getStartXapiDateTime()
    {
        if ($this->getStart() instanceof ilDateTime) {
            try {
                $xapiDateTime = ilCmiXapiDateTime::fromIliasDateTime($this->getStart());
            } catch (ilDateTimeException $e) {
                return null;
            }
        }
        
        return $xapiDateTime;
    }
    
    /**
     * @return ilCmiXapiDateTime|null
     */
    public function getEndXapiDateTime()
    {
        if ($this->getEnd() instanceof ilDateTime) {
            try {
                $xapiDateTime = ilCmiXapiDateTime::fromIliasDateTime($this->getEnd());
            } catch (ilDateTimeException $e) {
                return null;
            }
        }
        
        return $xapiDateTime;
    }
}
