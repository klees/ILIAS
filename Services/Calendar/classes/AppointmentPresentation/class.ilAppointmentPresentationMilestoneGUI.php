<?php declare(strict_types=1);

/**
 * ilAppointmentPresentationMilestoneGUI class presents milestones information.
 * @author            Jesús López Reyes <lopez@leifos.com>
 * @version           $Id$
 * @ilCtrl_IsCalledBy ilAppointmentPresentationMilestoneGUI: ilCalendarAppointmentPresentationGUI
 * @ingroup           ServicesCalendar
 */
class ilAppointmentPresentationMilestoneGUI extends ilAppointmentPresentationGUI implements ilCalendarAppointmentPresentation
{
    public function collectPropertiesAndActions() : void
    {
        global $DIC;

        $f = $DIC->ui()->factory();
        $r = $DIC->ui()->renderer();

        $appointment = $this->appointment;
        $completion = $appointment['event']->getCompletion();
        $users_resp = $appointment['event']->readResponsibleUsers();
        $cat_info = $this->getCatInfo();

        $this->addCommonSection($appointment, $cat_info['obj_id']);
        $this->addInfoSection($this->lng->txt("cal_app_info"));

        $users_list = array();
        foreach ($users_resp as $user) {
            $users_list[] = $this->getUserName($user['user_id']);
        }
        if (count($users_list) > 0) {
            $this->addInfoProperty($this->lng->txt("cal_responsible"), implode("<br>", $users_list));
            $this->addListItemProperty($this->lng->txt("cal_responsible"), implode("<br>", $users_list));
        }

        $this->addInfoProperty($this->lng->txt("cal_task_completion"), $completion . " %");
        $this->addListItemProperty($this->lng->txt("cal_task_completion"), $completion . " %");

        // last edited
        $this->addLastUpdate($appointment);
    }
}
