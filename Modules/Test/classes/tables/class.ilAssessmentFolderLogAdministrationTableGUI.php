<?php
/* Copyright (c) 1998-2013 ILIAS open source, Extended GPL, see docs/LICENSE */


include_once('./Services/Table/classes/class.ilTable2GUI.php');

/**
*
* @author Helmut Schottmüller <ilias@aurealis.de>
* @version $Id$
*
* @ingroup ModulesTest
*/

class ilAssessmentFolderLogAdministrationTableGUI extends ilTable2GUI
{
    /**
     * Constructor
     *
     * @access public
     * @param
     * @return
     */
    public function __construct($a_parent_obj, $a_parent_cmd, $a_write_access = false)
    {
        parent::__construct($a_parent_obj, $a_parent_cmd);

        global $DIC;
        $lng = $DIC['lng'];
        $ilCtrl = $DIC['ilCtrl'];

        $this->lng = $lng;
        $this->ctrl = $ilCtrl;
        $this->counter = 1;
        
        $this->setFormName('showlog');
        $this->setStyle('table', 'fullwidth');

        $this->addColumn('', '', '1%', true);
        $this->addColumn($this->lng->txt("title"), 'title', '50%');
        $this->addColumn($this->lng->txt("ass_log_count_datasets"), 'nr', '15%');
        $this->addColumn($this->lng->txt("ass_location"), '', '30%');

        $this->setRowTemplate("tpl.il_as_tst_assessment_log_administration_row.html", "Modules/Test");

        $this->setFormAction($this->ctrl->getFormAction($a_parent_obj, $a_parent_cmd));

        if ($a_write_access) {
            $this->addMultiCommand('deleteLog', $this->lng->txt('ass_log_delete_entries'));
            $this->setSelectAllCheckbox('chb_test');
            $this->enable('select_all');
        }

        $this->numericOrdering('nr');
        $this->setDefaultOrderField("title");
        $this->setDefaultOrderDirection("asc");
        
        $this->setPrefix('chb_test');
        
        $this->enable('header');
        $this->enable('sort');
    }

    /**
     * fill row
     * @access public
     * @param
     * @return void
     */
    public function fillRow(array $a_set) : void
    {
        $this->tpl->setVariable("TITLE", ilLegacyFormElementsUtil::prepareFormOutput($a_set['title']));
        $this->tpl->setVariable("NR", $a_set['nr']);
        $this->tpl->setVariable("TEST_ID", $a_set['id']);
        $this->tpl->setVariable("LOCATION_HREF", $a_set['location_href']);
        $this->tpl->setVariable("LOCATION_TXT", $a_set['location_txt']);
    }

    /**
     * {@inheritdoc}
     */
    public function numericOrdering(string $a_field) : bool
    {
        return 'nr' == $a_field;
    }
}
