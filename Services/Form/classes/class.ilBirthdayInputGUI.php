<?php

/**
 * This file is part of ILIAS, a powerful learning management system
 * published by ILIAS open source e-Learning e.V.
 * ILIAS is licensed with the GPL-3.0,
 * see https://www.gnu.org/licenses/gpl-3.0.en.html
 * You should have received a copy of said license along with the
 * source code, too.
 * If this is not the case or you just want to try ILIAS, you'll find
 * us at:
 * https://www.ilias.de
 * https://github.com/ILIAS-eLearning
 */

/**
 * This class represents a text property in a property form.
 *
 * @author Alexander Killing <killing@leifos.de>
 */
class ilBirthdayInputGUI extends ilDateTimeInputGUI
{
    public function getStartYear() : int
    {
        return date("Y") - 100;
    }
    
    protected function parseDatePickerConfig() : array
    {
        $config = parent::parseDatePickerConfig();
                
        $config["viewMode"] = "years";
        $config["calendarWeeks"] = false;
        $config["showTodayButton"] = false;
        
        return $config;
    }
}
