<?php

namespace ILIAS\GlobalScreen\Client;

use ILIAS\GlobalScreen\Scope\MainMenu\Collector\Renderer\Hasher;
use ILIAS\GlobalScreen\Scope\Notification\Factory\StandardNotificationGroup;

/******************************************************************************
 * This file is part of ILIAS, a powerful learning management system.
 * ILIAS is licensed with the GPL-3.0, you should have received a copy
 * of said license along with the source code.
 * If this is not the case or you just want to try ILIAS, you'll find
 * us at:
 *      https://www.ilias.de
 *      https://github.com/ILIAS-eLearning
 *****************************************************************************/

/**
 * Class Notifications
 * Handles Async Calls for the Notification Center
 * @package ILIAS\GlobalScreen\Client
 */
class Notifications
{
    use Hasher;
    
    /**
     * Collected set of collected notifications
     */
    protected array $notification_groups;
    /**
     * Name of the GET param used in the async calls
     */
    const MODE = "mode";
    /**
     * Value of the MODE GET param, if the Notification Center has been opened
     */
    const MODE_OPENED = "opened";
    /**
     * Value of the MODE GET param, if the Notification Center has been closed
     */
    const MODE_CLOSED = "closed";
    /**
     * NAME of the GET param, to indicate the item ID of the closed item
     */
    const ITEM_ID = "item_id";
    /**
     * Used to read the identifiers out of the GET param later
     */
    const NOTIFICATION_IDENTIFIERS = "notification_identifiers";
    /**
     * Location of the endpoint handling async notification requests
     */
    const NOTIFY_ENDPOINT = ILIAS_HTTP_PATH . "/src/GlobalScreen/Client/notify.php";
    protected array $identifiers_to_handle = [];
    protected ?string $single_identifier_to_handle;
    protected array $administrative_notifications = [];
    
    public function run() : void
    {
        /**
         * @DI $DI
         */
        global $DIC;
        $this->notification_groups = $DIC->globalScreen()->collector()->notifications()->getNotifications();
        $this->administrative_notifications = $DIC->globalScreen()->collector()->notifications()->getAdministrativeNotifications();
        $this->identifiers_to_handle = $DIC->http()->request()->getQueryParams()[self::NOTIFICATION_IDENTIFIERS] ?? [];
        $this->single_identifier_to_handle = $DIC->http()->request()->getQueryParams()[self::ITEM_ID] ?? null;
        
        $mode = 0;
        $query = $DIC->http()->wrapper()->query();
        if ($query->has(self::MODE)) {
            $mode = $query->retrieve(self::MODE, $DIC->refinery()->to()->string());
        }
        
        switch ($mode) {
            case self::MODE_OPENED:
                $this->handleOpened();
                break;
            case self::MODE_CLOSED:
                $this->handleClosed();
                break;
        }
    }
    
    /**
     * Loops through all available open callable provided by the notification
     * providers
     */
    private function handleOpened() : void
    {
        foreach ($this->notification_groups as $notification_group) {
            foreach ($notification_group->getNotifications() as $notification) {
                if (in_array($this->hash($notification->getProviderIdentification()->serialize()), $this->identifiers_to_handle, true)) {
                    $notification->getOpenedCallable()();
                }
            }
            if (in_array($this->hash($notification_group->getProviderIdentification()->serialize()), $this->identifiers_to_handle, true)) {
                $notification_group->getOpenedCallable()();
            }
        }
    }
    
    /**
     * Runs the closed callable if such a callable is provided
     */
    private function handleClosed() : void
    {
        foreach ($this->notification_groups as $notification_group) {
            foreach ($notification_group->getNotifications() as $notification) {
                if ($this->single_identifier_to_handle === $this->hash($notification->getProviderIdentification()->serialize())) {
                    if ($notification->hasClosedCallable()) {
                        $notification->getClosedCallable()();
                    }
                }
            }
        }
        foreach ($this->administrative_notifications as $administrative_notification) {
            if ($this->single_identifier_to_handle === $this->hash($administrative_notification->getProviderIdentification()->serialize())) {
                if ($administrative_notification->hasClosedCallable()) {
                    $administrative_notification->getClosedCallable()();
                }
            }
        }
    }
}
