<?php
/*
    +-----------------------------------------------------------------------------+
    | ILIAS open source                                                           |
    +-----------------------------------------------------------------------------+
    | Copyright (c) 1998-2006 ILIAS open source, University of Cologne            |
    |                                                                             |
    | This program is free software; you can redistribute it and/or               |
    | modify it under the terms of the GNU General Public License                 |
    | as published by the Free Software Foundation; either version 2              |
    | of the License, or (at your option) any later version.                      |
    |                                                                             |
    | This program is distributed in the hope that it will be useful,             |
    | but WITHOUT ANY WARRANTY; without even the implied warranty of              |
    | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the               |
    | GNU General Public License for more details.                                |
    |                                                                             |
    | You should have received a copy of the GNU General Public License           |
    | along with this program; if not, write to the Free Software                 |
    | Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA. |
    +-----------------------------------------------------------------------------+
*/

include_once("./Services/ContainerReference/classes/class.ilContainerReferenceAccess.php");

/**
*
*
* @author Stefan Meyer <smeyer.ilias@gmx.de>
* @version $Id$
*
*
* @ingroup ModulesCourseReference
*/

class ilObjCourseReferenceAccess extends ilContainerReferenceAccess
{
    /**
     * @inheritdoc
     */
    public function _checkAccess(string $cmd, string $permission, int $ref_id, int $obj_id, ?int $user_id = null) : bool
    {
        global $DIC;
        
        switch ($permission) {
            case 'visible':
            case 'read':
            case 'edit_learning_progress':
                include_once './Modules/CourseReference/classes/class.ilObjCourseReference.php';
                $target_ref_id = ilObjCourseReference::_lookupTargetRefId($obj_id);
                
                if (!$DIC->access()->checkAccessOfUser($user_id, $permission, $cmd, $target_ref_id)) {
                    return false;
                }
                break;
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public static function _preloadData(array $obj_ids, array $ref_ids) : void
    {
        global $DIC;

        $repository = new ilUserCertificateRepository();
        $coursePreload = new ilCertificateObjectsForUserPreloader($repository);
        $coursePreload->preLoad($DIC->user()->getId(), array_map(function ($objId) {
            return (int) \ilObjCourseReference::_lookupTargetId($objId);
        }, $obj_ids));
    }

    /**
     * @inheritdoc
     */
    public static function _getCommands($a_ref_id = 0) : array
    {
        global $DIC;
        
        if ($DIC->access()->checkAccess('write', '', $a_ref_id)) {
            // Only local (reference specific commands)
            $commands = array(
                array("permission" => "visible", "cmd" => "", "lang_var" => "show","default" => true),
                array("permission" => "write", "cmd" => "editReference", "lang_var" => "edit")
            );
        } else {
            include_once('./Modules/Course/classes/class.ilObjCourseAccess.php');
            $commands = ilObjCourseAccess::_getCommands();
        }
        return $commands;
    }
}
