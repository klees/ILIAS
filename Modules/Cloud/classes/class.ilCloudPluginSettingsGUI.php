<?php
/* Copyright (c) 1998-2009 ILIAS open source, Extended GPL, see docs/LICENSE */

require_once("./Services/Form/classes/class.ilPropertyFormGUI.php");
require_once("class.ilCloudUtil.php");

/**
 * Class ilCloudPluginSettingsGUI
 * Base class for the settings. Needs to be overwritten if the plugin needs custom settings.
 * @author  Timon Amstutz <timon.amstutz@ilub.unibe.ch>
 * @author  Martin Studer martin@fluxlabs.ch
 * @version $Id:
 * @ingroup ModulesCloud
 */
class ilCloudPluginSettingsGUI extends ilCloudPluginGUI
{
    protected ilObjCloud $cloud_object;
    protected ilPropertyFormGUI $form;
    private \ilGlobalTemplateInterface $main_tpl;

    public function __construct(string $plugin_service_class_name)
    {
        global $DIC;
        $this->main_tpl = $DIC->ui()->mainTemplate();
        parent::__construct($plugin_service_class_name);

        $DIC->language()->loadLanguageModule('content');
        $DIC->language()->loadLanguageModule('obj');
        $DIC->language()->loadLanguageModule('cntr');
    }

    public function setCloudObject(ilObjCloud $object): void
    {
        $this->cloud_object = $object;
    }

    /**
     * Edit Settings. This commands uses the form class to display an input form.
     */
    public function editSettings(): void
    {
        global $DIC;
        $tpl = $DIC['tpl'];
        $ilTabs = $DIC['ilTabs'];

        $ilTabs->activateTab("settings");

        try {
            $this->initSettingsForm();
            $this->getSettingsValues();
            $tpl->setContent($this->form->getHTML());
        } catch (Exception $e) {
            $this->main_tpl->setOnScreenMessage('failure', $e->getMessage());
        }
    }

    public function initSettingsForm(): void
    {
        global $DIC;
        $ilCtrl = $DIC['ilCtrl'];
        $lng = $DIC['lng'];

        $this->form = new ilPropertyFormGUI();

        // title
        $ti = new ilTextInputGUI($lng->txt("title"), "title");
        $ti->setRequired(true);
        $this->form->addItem($ti);

        // description
        $ta = new ilTextAreaInputGUI($lng->txt("description"), "desc");
        $this->form->addItem($ta);

        // online
        $cb = new ilCheckboxInputGUI($lng->txt("online"), "online");
        $this->form->addItem($cb);

        $folder = new ilTextInputGUI($lng->txt("cld_root_folder"), "root_folder");
        if (!$this->cloud_object->currentUserIsOwner()) {
            $folder->setDisabled(true);
            $folder->setInfo($lng->txt("cld_only_owner_has_permission_to_change_root_path"));
        }

        $folder->setMaxLength(255);
        $folder->setSize(50);
        $this->form->addItem($folder);

        $this->createPluginSection();
        $this->initPluginSettings();

        $this->initPresentationSection();

        $this->form->addCommandButton("updateSettings", $lng->txt("save"));

        $this->form->setTitle($lng->txt("cld_edit_Settings"));
        $this->form->setFormAction($ilCtrl->getFormActionByClass("ilCloudPluginSettingsGUI"));
    }

    protected function createPluginSection(): void
    {
        if (get_class($this) != "ilCloudPluginSettingsGUI" && $this->getMakeOwnPluginSection()) {
            global $DIC;
            $lng = $DIC['lng'];
            $section = new ilFormSectionHeaderGUI();
            $section->setTitle($this->cloud_object->getServiceName() . " " . $lng->txt("cld_service_specific_settings"));
            $this->form->addItem($section);
        }
    }

    protected function initPluginSettings(): void
    {
    }

    protected function initPresentationSection(): void
    {
        global $DIC;
        $section_appearance = new ilFormSectionHeaderGUI();
        $section_appearance->setTitle($DIC->language()->txt('cont_presentation'));
        $this->form->addItem($section_appearance);
        $DIC->object()->commonSettings()->legacyForm($this->form, $this->cloud_object)->addTileImage();
    }

    protected function getMakeOwnPluginSection(): bool
    {
        return true;
    }

    /**
     * Get values for edit Settings form
     */
    public function getSettingsValues(): void
    {
        $values["title"] = $this->cloud_object->getTitle();
        $values["desc"] = $this->cloud_object->getDescription();
        $values["online"] = $this->cloud_object->getOnline();
        $values["root_folder"] = $this->cloud_object->getRootFolder();
        $this->getPluginSettingsValues($values);
        $this->form->setValuesByArray($values, true);
    }

    protected function getPluginSettingsValues(): array
    {
    }

    public function updateSettings(): void
    {
        global $DIC;
        $tpl = $DIC['tpl'];
        $lng = $DIC['lng'];
        $ilCtrl = $DIC['ilCtrl'];
        $ilTabs = $DIC['ilTabs'];

        $ilTabs->activateTab("settings");

        try {
            $this->initSettingsForm();
            $this->initPresentationSection();
            if ($this->form->checkInput()) {
                $this->cloud_object->setTitle($this->form->getInput("title"));
                $this->cloud_object->setDescription($this->form->getInput("desc"));
                $this->updatePluginSettings();
                if (ilCloudUtil::normalizePath($this->form->getInput("root_folder")) != $this->cloud_object->getRootFolder()) {
                    $this->cloud_object->setRootFolder($this->form->getInput("root_folder"));
                    $this->cloud_object->setRootId($this->getService()->getRootId($this->cloud_object->getRootFolder()));
                }

                $this->cloud_object->setOnline($this->form->getInput("online"));
                $this->cloud_object->update();

                $DIC->object()->commonSettings()->legacyForm($this->form, $this->cloud_object)->saveTileImage();

                $this->main_tpl->setOnScreenMessage('success', $lng->txt("msg_obj_modified"), true);
                $ilCtrl->redirect($this, 'editSettings');
            }
        } catch (Exception $e) {
            $this->main_tpl->setOnScreenMessage('failure', $e->getMessage());
        }

        $this->form->setValuesByPost();
        $tpl->setContent($this->form->getHtml());
    }

    protected function updatePluginSettings(): void
    {
    }
}
