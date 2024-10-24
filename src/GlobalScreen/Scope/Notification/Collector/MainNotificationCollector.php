<?php namespace ILIAS\GlobalScreen\Scope\Notification\Collector;

use ILIAS\GlobalScreen\Collector\AbstractBaseCollector;
use ILIAS\GlobalScreen\Collector\ItemCollector;
use ILIAS\GlobalScreen\Scope\MainMenu\Collector\Renderer\Hasher;
use ILIAS\GlobalScreen\Scope\Notification\Factory\AdministrativeNotification;
use ILIAS\GlobalScreen\Scope\Notification\Factory\isItem;
use ILIAS\GlobalScreen\Scope\Notification\Factory\StandardNotificationGroup;
use ILIAS\GlobalScreen\Scope\Notification\Provider\NotificationProvider;

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
 * Class MainNotificationCollector
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class MainNotificationCollector extends AbstractBaseCollector implements ItemCollector
{
    use Hasher;
    
    /**
     * @var NotificationProvider[]
     */
    private array $providers = [];
    /**
     * @var isItem[]
     */
    private array $notifications = [];
    /**
     * @var AdministrativeNotification[]
     */
    private array $administrative_notifications = [];
    
    /**
     * MetaBarMainCollector constructor.
     * @param NotificationProvider[] $providers
     */
    public function __construct(array $providers)
    {
        $this->providers = $providers;
        $this->collectOnce();
    }
    
    /**
     * Generator yielding the Notifications from the set of providers
     * @return \Iterator<\ILIAS\GlobalScreen\Scope\Notification\Factory\isItem[]>
     */
    private function returnNotificationsFromProviders() : \Iterator
    {
        foreach ($this->providers as $provider) {
            yield $provider->getNotifications();
        }
    }
    
    /**
     * @return \Iterator<\ILIAS\GlobalScreen\Scope\Notification\Factory\AdministrativeNotification[]>
     */
    private function returnAdministrativeNotificationsFromProviders() : \Iterator
    {
        foreach ($this->providers as $provider) {
            yield $provider->getAdministrativeNotifications();
        }
    }
    
    public function collectStructure() : void
    {
        $this->notifications = array_merge([], ...iterator_to_array($this->returnNotificationsFromProviders()));
        $this->administrative_notifications = array_merge([], ...iterator_to_array($this->returnAdministrativeNotificationsFromProviders()));
    }
    
    public function filterItemsByVisibilty(bool $async_only = false) : void
    {
        $this->administrative_notifications = array_filter($this->administrative_notifications, static function (AdministrativeNotification $n) : bool {
            return $n->isVisible();
        });
    }
    
    public function prepareItemsForUIRepresentation() : void
    {
        // TODO: Implement prepareItemsForUIRepresentation() method.
    }
    
    public function cleanupItemsForUIRepresentation() : void
    {
        // TODO: Implement cleanupItemsForUIRepresentation() method.
    }
    
    public function sortItemsForUIRepresentation() : void
    {
        // TODO: Implement sortItemsForUIRepresentation() method.
    }
    
    /**
     * @inheritDoc
     */
    public function getItemsForUIRepresentation() : \Generator
    {
        yield from $this->notifications;
    }
    
    /**
     * @inheritDoc
     */
    public function hasItems() : bool
    {
        return (is_array($this->notifications) && count($this->notifications) > 0);
    }
    
    /**
     * Returns the sum of all old notifications values in the
     * Standard Notifications
     * @return int
     */
    public function getAmountOfOldNotifications() : int
    {
        if (is_array($this->notifications)) {
            $count = 0;
            foreach ($this->notifications as $notification) {
                if ($notification instanceof StandardNotificationGroup) {
                    foreach ($notification->getNotifications() as $s_notification) {
                        $count += $s_notification->getOldAmount();
                    }
                } else {
                    $count += $notification->getOldAmount();
                }
            }
            
            return $count;
        }
        
        return 0;
    }
    
    /**
     * Returns the sum of all new notifications values in the
     * Standard Notifications
     * @return int
     */
    public function getAmountOfNewNotifications() : int
    {
        if (is_array($this->notifications)) {
            $count = 0;
            foreach ($this->notifications as $notification) {
                if ($notification instanceof StandardNotificationGroup) {
                    foreach ($notification->getNotifications() as $s_notification) {
                        $count += $s_notification->getNewAmount();
                    }
                } else {
                    $count += $notification->getNewAmount();
                }
            }
            
            return $count;
        }
        
        return 0;
    }
    
    /**
     * Returns the set of collected informations
     * @return isItem[]
     */
    public function getNotifications() : array
    {
        return $this->notifications;
    }
    
    /**
     * @return AdministrativeNotification[]
     */
    public function getAdministrativeNotifications() : array
    {
        return $this->administrative_notifications;
    }
    
    /**
     * @return array
     */
    public function getNotificationsIdentifiersAsArray(bool $hashed = false) : array
    {
        $identifiers = [];
        foreach ($this->notifications as $notification) {
            if ($notification instanceof StandardNotificationGroup) {
                foreach ($notification->getNotifications() as $item) {
                    if ($hashed) {
                        $identifiers[] = $this->hash($item->getProviderIdentification()->serialize());
                    } else {
                        $identifiers[] = $item->getProviderIdentification()->serialize();
                    }
                }
            }
            if ($hashed) {
                $identifiers[] = $this->hash($notification->getProviderIdentification()->serialize());
            } else {
                $identifiers[] = $notification->getProviderIdentification()->serialize();
            }
        }
        
        return $identifiers;
    }
}
