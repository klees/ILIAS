<?php

/* Copyright (c) 1998-2021 ILIAS open source, GPLv3, see LICENSE */

use ILIAS\DI\UIServices;
use ILIAS\Exercise\Assignment\Mandatory;

/**
 * UI for random assignment
 * (ui)
 * @author Alexander Killing <killing@leifos.de>
 */
class ilExcRandomAssignmentGUI
{
    protected ilGlobalTemplateInterface $main_tpl;
    protected Mandatory\RandomAssignmentsManager $random_manager;
    protected ilToolbarGUI $toolbar;
    protected ilLanguage $lng;
    protected ilCtrl $ctrl;
    protected UIServices $ui;

    public function __construct(
        UIServices $ui,
        ilToolbarGUI $toolbar,
        ilLanguage $lng,
        ilCtrl $ctrl,
        Mandatory\RandomAssignmentsManager $random_manager
    ) {
        $this->main_tpl = $ui->mainTemplate();
        $this->ui = $ui;
        $this->random_manager = $random_manager;
        $this->toolbar = $toolbar;
        $this->ctrl = $ctrl;
        $this->lng = $lng;
    }

    public function executeCommand() : void
    {
        $ctrl = $this->ctrl;

        $next_class = $ctrl->getNextClass($this);
        $cmd = $ctrl->getCmd("startExercise");

        switch ($next_class) {
            default:
                if (in_array($cmd, array("startExercise"))) {
                    $this->$cmd();
                }
        }
    }

    /**
     * @throws ilCtrlException
     * @throws ilDatabaseException
     * @throws ilDateTimeException
     * @throws ilObjectNotFoundException
     */
    public function renderStartPage()
    {
        $toolbar = $this->toolbar;
        $lng = $this->lng;

        $but = $this->ui->factory()->button()->primary(
            $lng->txt("exc_start_exercise"),
            $this->ctrl->getLinkTarget($this, "startExercise")
        );
        $toolbar->addComponent($but);
        $info_gui = new ilInfoScreenGUI($this);

        $info_gui->addSection($lng->txt("exc_random_assignment"));
        $info_gui->addProperty(
            " ",
            $lng->txt("exc_random_assignment_info")
        );
        $info_gui->addProperty(
            $lng->txt("exc_rand_overall_ass"),
            $this->random_manager->getTotalNumberOfAssignments()
        );
        $info_gui->addProperty(
            $lng->txt("exc_rand_nr_mandatory"),
            $this->random_manager->getNumberOfMandatoryAssignments()
        );
        $this->main_tpl->setContent($info_gui->getHTML());
    }
    
    protected function startExercise() : void
    {
        $this->random_manager->startExercise();
        $this->ctrl->redirectByClass("ilObjExerciseGUI", "showOverview");
    }
}
