<?php declare(strict_types=1);
/* Copyright (c) 1998-2021 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * Class ilMailMailingListAddressType
 * @author Michael Jansen <mjansen@databay.de>
 */
class ilMailMailingListAddressType extends ilBaseMailAddressType
{
    private ilMailingLists $lists;

    public function __construct(
        ilMailAddressTypeHelper $typeHelper,
        ilMailAddress $address,
        ilLogger $logger,
        ilMailingLists $lists
    ) {
        parent::__construct($typeHelper, $address, $logger);

        $this->lists = $lists;
    }

    protected function isValid(int $senderId) : bool
    {
        $valid = $this->lists->mailingListExists($this->address->getMailbox());

        if (!$valid) {
            $this->logger->debug(sprintf(
                "Mailing list not  valid: '%s'",
                $this->address->getMailbox()
            ));
            $this->pushError('mail_no_valid_mailing_list', [$this->address->getMailbox()]);
        }

        return $valid;
    }

    public function resolve() : array
    {
        $usrIds = [];

        if ($this->lists->mailingListExists($this->address->getMailbox())) {
            foreach ($this->lists->getCurrentMailingList()->getAssignedEntries() as $entry) {
                $usrIds[] = $entry['usr_id'];
            }

            $this->logger->debug(sprintf(
                "Found the following user ids for address (mailing list title) '%s': %s",
                $this->address->getMailbox(),
                implode(', ', array_unique($usrIds))
            ));
        } else {
            $this->logger->debug(sprintf(
                "Did not find any user ids for address (mailing list title) '%s'",
                $this->address->getMailbox()
            ));
        }

        return array_unique($usrIds);
    }
}
