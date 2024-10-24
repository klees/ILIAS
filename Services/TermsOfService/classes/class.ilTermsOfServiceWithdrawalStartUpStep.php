<?php declare(strict_types=1);

/* Copyright (c) 1998-2018 ILIAS open source, Extended GPL, see docs/LICENSE */

use ILIAS\DI\Container;
use ILIAS\Init\StartupSequence\StartUpSequenceStep;

/**
 * Class ilTermsOfServiceWithdrawalStartUpStep
 * @author Maximilian Becker <mbecker@databay.de>
 * @author Michael Jansen <mjansen@databay.de>
 */
class ilTermsOfServiceWithdrawalStartUpStep extends StartUpSequenceStep
{
    private Container $dic;

    public function __construct(Container $dic)
    {
        $this->dic = $dic;
    }

    public function shouldStoreRequestTarget() : bool
    {
        return true;
    }

    public function isInFulfillment() : bool
    {
        return (
            strtolower($this->dic->ctrl()->getCmdClass()) === strtolower(ilPersonalProfileGUI::class) && (
                strtolower($this->dic->ctrl()->getCmd()) === 'showuseragreement' ||
                strtolower($this->dic->ctrl()->getCmd()) === 'confirmwithdrawal' ||
                strtolower($this->dic->ctrl()->getCmd()) === 'showconsentwithdrawalconfirmation' ||
                strtolower($this->dic->ctrl()->getCmd()) === 'cancelwithdrawal' ||
                strtolower($this->dic->ctrl()->getCmd()) === 'withdrawacceptance' ||
                strtolower($this->dic->ctrl()->getCmd()) === 'rejectwithdrawal'
            )
        );
    }

    public function shouldInterceptRequest() : bool
    {
        if ($this->isInFulfillment()) {
            return false;
        }

        if ($this->dic->user()->getPref('consent_withdrawal_requested')) {
            return true;
        }

        return false;
    }

    public function execute() : void
    {
        $this->dic->ctrl()->redirectByClass(
            [ilDashboardGUI::class, ilPersonalProfileGUI::class],
            'showConsentWithdrawalConfirmation'
        );
    }
}
