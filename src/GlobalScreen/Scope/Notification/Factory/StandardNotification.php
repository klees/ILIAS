<?php namespace ILIAS\GlobalScreen\Scope\Notification\Factory;

use ILIAS\UI\Component\Item\Notification as NotificationItem;

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
 * Class Notification
 * The default Notification mapping currently to one UI Notification Item component
 * @author Timon Amstutz
 */
class StandardNotification extends AbstractBaseNotification implements isStandardItem, hasAmount
{
    
    /**
     * UI Component mapping to this item
     */
    private NotificationItem $notification_item;
    /**
     * Amount of old notes, the notification contains.
     * @see hasAmount
     */
    private int $old_amount = 0;
    /**
     * Amount of old notes, the notification contains.
     * @see hasAmount
     */
    private int $new_amount = 1;
    
    public function withNotificationItem(NotificationItem $notification_item) : self
    {
        $clone = clone $this;
        $clone->notification_item = $notification_item;
        
        return $clone;
    }
    
    /**
     * @return NotificationItem
     */
    public function getNotificationItem() : NotificationItem
    {
        return $this->notification_item;
    }
    
    /**
     * @inheritdoc
     */
    public function withOldAmount(int $amount = 0) : StandardNotification
    {
        $clone = clone $this;
        $clone->old_amount = $amount;
        
        return $clone;
    }
    
    /**
     * @inheritdoc
     */
    public function withNewAmount(int $amount = 0) : StandardNotification
    {
        $clone = clone $this;
        $clone->new_amount = $amount;
        
        return $clone;
    }
    
    /**
     * @inheritdoc
     */
    public function getOldAmount() : int
    {
        return $this->old_amount;
    }
    
    /**
     * @inheritdoc
     */
    public function getNewAmount() : int
    {
        return $this->new_amount;
    }
}
