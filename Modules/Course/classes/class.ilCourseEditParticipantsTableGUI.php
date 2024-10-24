<?php declare(strict_types=0);
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

/**
 * @author  Stefan Meyer <smeyer.ilias@gmx.de>
 * @version $Id$
 * @ingroup ModulesCourse
 */
class ilCourseEditParticipantsTableGUI extends ilTable2GUI
{
    protected ilObjCourse $rep_object;
    protected ilPrivacySettings $privacy;
    protected ilCourseParticipants $participants;

    protected ilAccessHandler $access;
    protected ilObjUser $user;

    /**
     * Holds the local roles of the course object.
     * This variable is an associative array.
     * - The key is the localized name of the role (for example
     *   'Course Administrator')
     * - The value is an associative array with the keys 'role_id' and
     *   'title'.
     */
    private array $localCourseRoles;

    public function __construct(object $a_parent_obj, ilObjCourse $rep_object)
    {
        global $DIC;

        $this->rep_object = $rep_object;
        $this->privacy = ilPrivacySettings::getInstance();
        $this->participants = ilCourseParticipants::_getInstanceByObjId($this->rep_object->getId());

        $this->access = $DIC->access();
        $this->user = $DIC->user();

        parent::__construct($a_parent_obj, 'editMembers');
        $this->lng->loadLanguageModule('crs');
        $this->setFormName('participants');
        $this->setFormAction($this->ctrl->getFormAction($a_parent_obj));

        $this->addColumn($this->lng->txt('name'), 'name', '20%');
        $this->addColumn($this->lng->txt('login'), 'login', '25%');

        if ($this->privacy->enabledCourseAccessTimes()) {
            $this->addColumn($this->lng->txt('last_access'), 'access_time');
        }
        $this->addColumn($this->lng->txt('crs_passed'), 'passed');
        $this->addColumn($this->lng->txt('crs_blocked'), 'blocked');
        $this->addColumn($this->lng->txt('crs_mem_contact'), 'contact');
        $this->addColumn($this->lng->txt('crs_notification'), 'notification');
        $this->addColumn($this->lng->txt('objs_role'), 'roles');

        $this->addCommandButton('updateParticipants', $this->lng->txt('save'));
        $this->addCommandButton('participants', $this->lng->txt('cancel'));
        $this->setRowTemplate("tpl.edit_participants_row.html", "Modules/Course");
        $this->disable('sort');
        $this->enable('header');
        $this->enable('numinfo');
        $this->disable('select_all');

        // Performance improvement: We read the local course roles
        // only once, instead of reading them for each row in method fillRow().
        $this->localCourseRoles = array();
        foreach ($this->rep_object->getLocalCourseRoles(false) as $title => $role_id) {
            $this->localCourseRoles[ilObjRole::_getTranslation($title)] = array('role_id' => $role_id,
                                                                                'title' => $title
            );
        }
    }

    protected function fillRow(array $a_set) : void
    {
        $hasEditPermissionAccess =
            (
                $this->access->checkAccess('edit_permission', '', $this->rep_object->getRefId()) or
                $this->participants->isAdmin($this->user->getId())
            );

        $this->tpl->setVariable('VAL_ID', $a_set['usr_id']);
        $this->tpl->setVariable('VAL_NAME', $a_set['lastname'] . ', ' . $a_set['firstname']);

        $this->tpl->setVariable('VAL_LOGIN', $a_set['login']);

        if ($this->privacy->enabledCourseAccessTimes()) {
            $this->tpl->setVariable('VAL_ACCESS', $a_set['access_time']);
        }
        $this->tpl->setVariable('VAL_CONTACT_CHECKED', $a_set['contact'] ? 'checked="checked"' : '');
        $this->tpl->setVariable('VAL_NOTIFICATION_CHECKED', $a_set['notification'] ? 'checked="checked"' : '');
        $this->tpl->setVariable('VAL_PASSED_CHECKED', $a_set['passed'] ? 'checked="checked"' : '');
        $this->tpl->setVariable('VAL_BLOCKED_CHECKED', $a_set['blocked'] ? 'checked="checked"' : '');

        $this->tpl->setVariable('NUM_ROLES', count($this->participants->getRoles()));

        $assigned = $this->participants->getAssignedRoles($a_set['usr_id']);
        foreach ($this->localCourseRoles as $localizedTitle => $roleData) {
            if ($hasEditPermissionAccess || substr($roleData['title'], 0, 12) != 'il_crs_admin') {
                $this->tpl->setCurrentBlock('roles');
                $this->tpl->setVariable('ROLE_ID', $roleData['role_id']);
                $this->tpl->setVariable('ROLE_NAME', $localizedTitle);

                if (in_array($roleData['role_id'], $assigned)) {
                    $this->tpl->setVariable('ROLE_CHECKED', 'selected="selected"');
                }
                $this->tpl->parseCurrentBlock();
            }
        }
    }
}
