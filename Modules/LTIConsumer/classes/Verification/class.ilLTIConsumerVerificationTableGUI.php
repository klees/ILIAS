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
 * List all completed course for current user
 *
 * @author      Uwe Kohnle <kohnle@internetlehrer-gmbh.de>
 * @author      Björn Heyser <info@bjoernheyser.de>
 * @author      Stefan Schneider <info@eqsoft.de>
 *
 * @package     Module/LTIConsumer
 */
class ilLTIConsumerVerificationTableGUI extends ilTable2GUI
{
    /**
     * Constructor
     * @param ilObjLTIConsumerVerificationGUI|null $a_parent_obj
     * @param string|null                          $a_parent_cmd
     * @throws ilCtrlException
     */
    public function __construct(?ilObjLTIConsumerVerificationGUI $a_parent_obj, ?string $a_parent_cmd = "")
    {
        global $DIC; /* @var \ILIAS\DI\Container $DIC */

        parent::__construct($a_parent_obj, $a_parent_cmd);
        
        $this->addColumn($this->lng->txt("title"), "title");
        $this->addColumn($this->lng->txt("passed"), "passed");
        $this->addColumn($this->lng->txt("action"), "");
        
        $this->setTitle($this->lng->txt("ltiv_create"));
        $this->setDescription($this->lng->txt("ltiv_create_info"));
        
        $this->setRowTemplate("tpl.lti_verification_row.html", "Modules/LTIConsumer");
        $this->setFormAction($DIC->ctrl()->getFormAction($a_parent_obj, $a_parent_cmd));
        
        $this->getItems();
    }
    
    /**
     * Get all completed tests
     */
    protected function getItems() : void
    {
        global $DIC; /* @var \ILIAS\DI\Container $DIC */

        $userCertificateRepository = new ilUserCertificateRepository(
            $DIC->database(),
            $DIC->logger()->root()
        );

        $certificateArray = $userCertificateRepository->fetchActiveCertificatesByTypeForPresentation(
            $DIC->user()->getId(),
            'lti'
        );

        $data = array();

        foreach ($certificateArray as $certificate) {
            $data[] = array(
                'id' => $certificate->getUserCertificate()->getObjId(),
                'title' => $certificate->getObjectTitle(),
                'passed' => true
            );
        }

        $this->setData($data);
    }
    
    /**
     * Fill template row
     * @param array $a_set
     */
    protected function fillRow(array $a_set) : void
    {
        global $DIC; /* @var \ILIAS\DI\Container $DIC */
        
        $this->tpl->setVariable("TITLE", $a_set["title"]);
        $this->tpl->setVariable("PASSED", ($a_set["passed"]) ? $this->lng->txt("yes") :
            $this->lng->txt("no"));
        
        if ($a_set["passed"]) {
            $DIC->ctrl()->setParameter($this->parent_obj, "lti_id", $a_set["id"]);
            $action = $DIC->ctrl()->getLinkTarget($this->parent_obj, "save");
            $this->tpl->setVariable("URL_SELECT", $action);
            $this->tpl->setVariable("TXT_SELECT", $this->lng->txt("select"));
        }
    }
}
