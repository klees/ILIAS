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
 * Description of class
 *
 * @author Stefan Meyer <meyer@leifos.com>
 * @ingroup ModulesScormAicc
 */
class ilSCORMTrackingUsersTableGUI extends ilTable2GUI
{
    private int $obj_id = 0;
    private array $filter;

    /**
     * Constructor
     * @param             $a_obj_id
     * @param object|null $a_parent_obj
     * @param string      $a_parent_cmd
     */
    public function __construct($a_obj_id, ?object $a_parent_obj, string $a_parent_cmd)
    {
        $this->obj_id = $a_obj_id;

        $this->setId('sco_tr_usrs_' . $this->obj_id);
        parent::__construct($a_parent_obj, $a_parent_cmd);
        $this->initFilter();
    }

    /**
     * Get Obj id
     * @return int
     */
    public function getObjId() : int
    {
        return $this->obj_id;
    }

    /**
     * Parse table content
     * @return void
     * @throws ilDateTimeException
     */
    public function parse() : void
    {
        $this->initTable();

        $users = $this->getParentObject()->object->getTrackedUsers((string) $this->filter['lastname']);
        $attempts = $this->getParentObject()->object->getAttemptsForUsers();
        $versions = $this->getParentObject()->object->getModuleVersionForUsers();
        
        $data = array();
        foreach ($users as $user) {
            $tmp = array();
            $tmp['user'] = $user['user_id'];
            $tmp['name'] = $user['lastname'] . ', ' . $user['firstname'];
            $dt = new ilDateTime($user['last_access'], IL_CAL_DATETIME);
            $tmp['last_access'] = $dt->get(IL_CAL_UNIX);
            $tmp['attempts'] = (int) $attempts[$user['user_id']];
            $tmp['version'] = (int) $versions[$user['user_id']];

            $data[] = $tmp;
        }
        $this->determineOffsetAndOrder();
        $orderField = $this->getOrderField();
        $orderDirection = $this->getOrderDirection();
        if (in_array(ilUtil::stripSlashes($orderField), ['user', 'attempts', 'version'])) {
            $this->setExternalSorting(true);
            $data = ilArrayUtil::sortArray(
                $data,
                $orderField,
                $orderDirection,
                true
            );
        }
        $this->setData($data);
    }

    /**
     * @return void
     * @throws Exception
     */
    public function initFilter() : void
    {
        $item = $this->addFilterItemByMetaType("lastname", ilTable2GUI::FILTER_TEXT);
        $this->filter["lastname"] = $item->getValue();
    }

    /**
     * Fill row template
     * @param array $a_set
     */
    protected function fillRow(array $a_set) : void
    {
        global $DIC;
        $ilCtrl = $DIC->ctrl();

        $this->tpl->setVariable('CHECKBOX_ID', $a_set['user']);
        $this->tpl->setVariable('VAL_USERNAME', $a_set['name']);

        // $ilCtrl->setParameter($this->getParentObject(),'user_id',$a_set['user']);
        // $this->tpl->setVariable('LINK_ITEM', $ilCtrl->getLinkTarget($this->getParentObject(),'showTrackingItem'));

        $this->tpl->setVariable('VAL_LAST', ilDatePresentation::formatDate(new ilDateTime($a_set['last_access'], IL_CAL_UNIX)));
        $this->tpl->setVariable('VAL_ATTEMPT', (int) $a_set['attempts']);
        $this->tpl->setVariable('VAL_VERSION', (string) $a_set['version']);
    }

    /**
     * Init table
     */
    protected function initTable() : void
    {
        global $DIC;
        $ilCtrl = $DIC->ctrl();

        $this->setFilterCommand('applyUserTableFilter');
        $this->setResetCommand('resetUserTableFilter');

        $this->setDisableFilterHiding(false);

        $this->setFormAction($ilCtrl->getFormAction($this->getParentObject()));
        $this->setRowTemplate('tpl.scorm_track_items.html', 'Modules/ScormAicc');
        $this->setTitle($this->lng->txt('cont_tracking_items'));

        $this->addColumn('', '', '1px');
        $this->addColumn($this->lng->txt('user'), 'name', '35%');
        $this->addColumn($this->lng->txt('last_access'), 'last_access', '25%');
        $this->addColumn($this->lng->txt('attempts'), 'attempts', '20%');
        $this->addColumn($this->lng->txt('version'), 'version', '20%');

        $this->enable('select_all');
        $this->setSelectAllCheckbox('user');

        $this->addMultiCommand('deleteTrackingForUser', $this->lng->txt('delete'));
        $this->addMultiCommand('exportSelectionUsers', $this->lng->txt('export'));
    }
}
