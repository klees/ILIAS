<?php declare(strict_types=1);
/* Copyright (c) 1998-2021 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * Class ilMailOptionsFormGUI
 */
class ilMailOptionsFormGUI extends ilPropertyFormGUI
{
    protected object $parentGui;
    protected string $positiveCmd = '';
    protected ilMailOptions $options;

    /**
     * @param ilMailOptions $options
     * @param object $parentGui
     * @param string $positiveCmd
     */
    public function __construct(ilMailOptions $options, object $parentGui, string $positiveCmd)
    {
        global $DIC;

        if (!method_exists($parentGui, 'executeCommand')) {
            throw new InvalidArgumentException(sprintf(
                'Parameter $parentGui must be ilCtrl enabled by implementing executeCommand(), %s given.',
                is_object($parentGui) ? get_class($parentGui) : var_export($parentGui, true)
            ));
        }

        parent::__construct();

        $this->options = $options;
        $this->parentGui = $parentGui;
        $this->positiveCmd = $positiveCmd;

        $this->init();
    }

    protected function init() : void
    {
        $this->setTitle($this->lng->txt('mail_settings'));
        $this->setFormAction($this->ctrl->getFormAction($this->parentGui, $this->positiveCmd));

        if ($this->settings->get('usr_settings_hide_mail_incoming_mail', '0') !== '1') {
            $incoming_mail_gui = new ilIncomingMailInputGUI(
                $this->lng->txt('mail_incoming'),
                'incoming_type',
                false
            );
            $this->addItem($incoming_mail_gui);
        }

        $options = [];
        for ($i = 50; $i <= 80; $i++) {
            $options[$i] = $i;
        }
        $si = new ilSelectInputGUI($this->lng->txt('linebreak'), 'linebreak');
        $si->setOptions($options);
        $this->addItem($si);

        $ta = new ilTextAreaInputGUI($this->lng->txt('signature'), 'signature');
        $ta->setRows(10);
        $ta->setCols(60);
        $this->addItem($ta);

        if ($this->settings->get('mail_notification', '0')) {
            $cb = new ilCheckboxInputGUI(
                $this->lng->txt('cron_mail_notification'),
                'cronjob_notification'
            );
            $cb->setInfo($this->lng->txt('mail_cronjob_notification_info'));
            $cb->setValue('1');
            $this->addItem($cb);
        }

        $this->addCommandButton($this->positiveCmd, $this->lng->txt('save'));
    }

    public function save() : bool
    {
        if (!$this->checkInput()) {
            return false;
        }

        if (
            $this->settings->get('usr_settings_hide_mail_incoming_mail', '0') !== '1' &&
            $this->settings->get('usr_settings_disable_mail_incoming_mail', '0') !== '1'
        ) {
            $incoming_type = (int) $this->getInput('incoming_type');

            $mail_address_option = $this->options->getEmailAddressMode();
            switch ($incoming_type) {
                case ilMailOptions::INCOMING_EMAIL:
                    $mail_address_option = (int) $this->getInput('mail_address_option');
                    break;

                case ilMailOptions::INCOMING_BOTH:
                    $mail_address_option = (int) $this->getInput('mail_address_option_both');
                    break;
            }
        } else {
            $incoming_type = $this->options->getIncomingType();
            $mail_address_option = $this->options->getEmailAddressMode();
        }

        $this->options->setLinebreak((int) $this->getInput('linebreak'));
        $this->options->setSignature($this->getInput('signature'));
        $this->options->setIsCronJobNotificationStatus((bool) $this->getInput('cronjob_notification'));
        $this->options->setIncomingType($incoming_type);
        $this->options->setEmailAddressMode($mail_address_option);

        $this->options->updateOptions();

        return true;
    }

    public function populate() : void
    {
        $data = [
            'linebreak' => $this->options->getLinebreak(),
            'signature' => $this->options->getSignature(),
            'cronjob_notification' => $this->options->isCronJobNotificationEnabled(),
        ];

        if ($this->settings->get('usr_settings_hide_mail_incoming_mail', '0') !== '1') {
            $data['incoming_type'] = $this->options->getIncomingType();

            $mail_address_option = $this->options->getEmailAddressMode();

            $data['mail_address_option'] = $mail_address_option;
            $data['mail_address_option_both'] = $mail_address_option;
        }

        $this->setValuesByArray($data);
    }
}
