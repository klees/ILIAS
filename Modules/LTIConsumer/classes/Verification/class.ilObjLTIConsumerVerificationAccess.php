<?php

/* Copyright (c) 1998-2019 ILIAS open source, Extended GPL, see docs/LICENSE */


/**
 * Class ilObjLTIConsumerVerificationAccess
 *
 * @author      Uwe Kohnle <kohnle@internetlehrer-gmbh.de>
 * @author      Björn Heyser <info@bjoernheyser.de>
 * @author      Stefan Schneider <info@eqsoft.de>
 *
 * @package     Module/LTIConsumer
 */
class ilObjLTIConsumerVerificationAccess extends ilObjectAccess
{
    public static function _getCommands()
    {
        $commands = array();
        $commands[] = array("permission" => "read", "cmd" => "view", "lang_var" => "show", "default" => true);
        return $commands;
    }
    
    public static function _checkGoto($a_target)
    {
        global $DIC; /* @var \ILIAS\DI\Container $DIC */
        
        $t_arr = explode("_", $a_target);
        
        // #11021
        // personal workspace context: do not force normal login
        if (isset($t_arr[2]) && $t_arr[2] == "wsp") {
            include_once "Services/PersonalWorkspace/classes/class.ilSharedResourceGUI.php";
            return ilSharedResourceGUI::hasAccess($t_arr[1]);
        }
        
        if ($DIC->access()->checkAccess("read", "", $t_arr[1])) {
            return true;
        }
        return false;
    }
}