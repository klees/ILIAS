<?php declare(strict_types=1);
/* Copyright (c) 1998-2020 ILIAS open source, Extended GPL, see docs/LICENSE */

use ILIAS\UI\Factory;
use ILIAS\UI\Renderer;
use ILIAS\UI\Component\Input\Container\Form\Standard as Form;
use ILIAS\ContentPage\GlobalSettings\Storage;
use ILIAS\ContentPage\GlobalSettings\StorageImpl;
use ILIAS\UI\Component\Component;
use ILIAS\HTTP\GlobalHttpState;

/**
 * @ilCtrl_Calls ilObjContentPageAdministrationGUI: ilPermissionGUI
 */
class ilObjContentPageAdministrationGUI extends ilObjectGUI
{
    private const CMD_VIEW = 'view';
    private const CMD_EDIT = 'edit';
    private const CMD_SAVE = 'save';
    private const F_READING_TIME = 'reading_time';

    private GlobalHttpState $http;
    private Factory $uiFactory;
    private Renderer $uiRenderer;
    protected ILIAS\Refinery\Factory $refinery;
    private Storage $settingsStorage;
    protected ilErrorHandling $error;

    public function __construct(array $a_data, int $a_id, bool $a_call_by_reference = true, bool $a_prepare_output = true)
    {
        global $DIC;

        $this->type = 'cpad';
        parent::__construct($a_data, $a_id, $a_call_by_reference, $a_prepare_output);
        $this->lng->loadLanguageModule($this->type);

        $this->uiFactory = $DIC->ui()->factory();
        $this->uiRenderer = $DIC->ui()->renderer();
        $this->http = $DIC->http();
        $this->refinery = $DIC->refinery();
        $this->error = $DIC['ilErr'];
        $this->settingsStorage = new StorageImpl($DIC->settings());
    }

    public function getAdminTabs() : void
    {
        if ($this->rbac_system->checkAccess('visible,read', $this->object->getRefId())) {
            $this->tabs_gui->addTarget('settings', $this->ctrl->getLinkTargetByClass(self::class, self::CMD_EDIT));
        }

        if ($this->rbac_system->checkAccess('edit_permission', $this->object->getRefId())) {
            $this->tabs_gui->addTarget(
                'perm_settings',
                $this->ctrl->getLinkTargetByClass(ilPermissionGUI::class, 'perm'),
                [],
                ilPermissionGUI::class
            );
        }
    }

    public function executeCommand() : void
    {
        if (!$this->rbac_system->checkAccess('visible,read', $this->object->getRefId())) {
            $this->error->raiseError($this->lng->txt('no_permission'), $this->error->WARNING);
        }

        $nextClass = $this->ctrl->getNextClass($this);
        $cmd = $this->ctrl->getCmd();
        $this->prepareOutput();

        switch (strtolower($nextClass)) {
            case strtolower(ilPermissionGUI::class):
                $this->tabs_gui->setTabActive('perm_settings');
                $perm_gui = new ilPermissionGUI($this);
                $this->ctrl->forwardCommand($perm_gui);
                break;

            default:
                switch ($cmd) {
                    case self::CMD_VIEW:
                    case self::CMD_EDIT:
                        $this->edit();
                        break;
                    case self::CMD_SAVE:
                        $this->save();
                        break;
                    default:
                        throw new RuntimeException(__METHOD__ . ' :: Unknown command ' . $cmd);
                }
        }
    }

    private function getForm(array $values = []) : Form
    {
        $action = $this->ctrl->getLinkTargetByClass(self::class, self::CMD_SAVE);

        $readingTimeStatus = $this->uiFactory
            ->input()
            ->field()
            ->checkbox(
                $this->lng->txt('cpad_reading_time_status'),
                $this->lng->txt('cpad_reading_time_status_desc')
            );

        if (isset($values[self::F_READING_TIME])) {
            $readingTimeStatus = $readingTimeStatus->withValue($values[self::F_READING_TIME]);
        }

        $section = $this->uiFactory->input()->field()->section(
            [self::F_READING_TIME => $readingTimeStatus],
            $this->lng->txt('settings')
        );

        return $this->uiFactory
            ->input()
            ->container()
            ->form()
            ->standard($action, [$section])
            ->withAdditionalTransformation($this->refinery->custom()->transformation(static function ($values) : array {
                return array_merge(...$values);
            }));
    }

    /**
     * @param Component[] $components
     */
    protected function show(array $components) : void
    {
        $this->tpl->setContent(
            $this->uiRenderer->render($components)
        );
    }

    protected function edit() : void
    {
        $values = [
            self::F_READING_TIME => $this->settingsStorage->getSettings()->isReadingTimeEnabled(),
        ];

        $form = $this->getForm($values);

        $this->show([$form]);
    }

    protected function save() : void
    {
        if (!$this->checkPermissionBool('write')) {
            $this->error->raiseError($this->lng->txt('permission_denied'), $this->error->MESSAGE);
        }

        $form = $this->getForm()->withRequest($this->http->request());
        $data = $form->getData();
        if ($data) {
            $readingTime = $data[self::F_READING_TIME];
            $settings = $this->settingsStorage->getSettings()
                ->withDisabledReadingTime();
            if ($readingTime) {
                $settings = $settings->withEnabledReadingTime();
            }
            $this->settingsStorage->store($settings);
        }

        $this->show(
            [$this->uiFactory->messageBox()->success($this->lng->txt('saved_successfully')), $form]
        );
    }
}
