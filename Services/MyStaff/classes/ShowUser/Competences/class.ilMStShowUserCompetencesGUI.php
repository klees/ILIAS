<?php

use ILIAS\DI\Container;
use ILIAS\MyStaff\ilMyStaffAccess;

/**
 * Class ilMStShowUserCompetencesGUI
 * @author            Theodor Truffer <tt@studer-raimann.ch>
 * @ilCtrl_IsCalledBy ilMStShowUserCompetencesGUI: ilMStShowUserGUI
 */
class ilMStShowUserCompetencesGUI
{
    const CMD_SHOW_SKILLS = 'showSkills';
    const CMD_INDEX = self::CMD_SHOW_SKILLS;
    const SUB_TAB_SKILLS = 'skills';
    private int $usr_id;
    protected ilTable2GUI $table;
    protected ilMyStaffAccess $access;
    private Container $dic;
    private \ilGlobalTemplateInterface $main_tpl;

    public function __construct(Container $dic)
    {
        global $DIC;
        $this->main_tpl = $DIC->ui()->mainTemplate();
        $this->dic = $dic;
        $this->access = ilMyStaffAccess::getInstance();

        $this->usr_id = $this->dic->http()->request()->getQueryParams()['usr_id'];
        $this->dic->ctrl()->setParameter($this, 'usr_id', $this->usr_id);
    }

    protected function checkAccessOrFail() : void
    {
        if (!$this->usr_id) {
            $this->main_tpl->setOnScreenMessage('failure', $this->dic->language()->txt("permission_denied"), true);
            $this->dic->ctrl()->redirectByClass(ilDashboardGUI::class, "");
        }

        if ($this->access->hasCurrentUserAccessToMyStaff()
            && $this->access->hasCurrentUserAccessToUser($this->usr_id)
        ) {
            return;
        } else {
            $this->main_tpl->setOnScreenMessage('failure', $this->dic->language()->txt("permission_denied"), true);
            $this->dic->ctrl()->redirectByClass(ilDashboardGUI::class, "");
        }
    }

    final public function executeCommand() : void
    {
        $this->checkAccessOrFail();

        $cmd = $this->dic->ctrl()->getCmd();
        $next_class = $this->dic->ctrl()->getNextClass();

        switch ($next_class) {
            default:
                switch ($cmd) {
                    case self::CMD_INDEX:
                    case self::CMD_SHOW_SKILLS:
                    default:
                        $this->addSubTabs(self::SUB_TAB_SKILLS);
                        $this->showSkills();
                        break;
                }
        }
    }

    protected function addSubTabs(string $active_sub_tab) : void
    {
        $this->dic->language()->loadLanguageModule('skmg');
        $this->dic->tabs()->addSubTab(
            self::SUB_TAB_SKILLS,
            $this->dic->language()->txt('skmg_selected_skills'),
            $this->dic->ctrl()->getLinkTarget($this, self::CMD_SHOW_SKILLS)
        );

        $this->dic->tabs()->activateSubTab($active_sub_tab);
    }

    protected function showSkills() : void
    {
        $skills_gui = new ilPersonalSkillsGUI();
        $skills = ilPersonalSkill::getSelectedUserSkills($this->usr_id);
        $html = '';
        foreach ($skills as $skill) {
            $html .= $skills_gui->getSkillHTML($skill["skill_node_id"], $this->usr_id);
        }
        $this->dic->ui()->mainTemplate()->setContent($html);
    }
}
