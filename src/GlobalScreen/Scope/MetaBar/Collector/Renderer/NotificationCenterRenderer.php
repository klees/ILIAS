<?php namespace ILIAS\GlobalScreen\Scope\MetaBar\Collector\Renderer;

use ILIAS\GlobalScreen\Client\Notifications as ClientNotifications;
use ILIAS\GlobalScreen\Collector\Renderer\isSupportedTrait;
use ILIAS\GlobalScreen\Scope\MetaBar\Factory\isItem;
use ILIAS\GlobalScreen\Scope\MetaBar\Factory\NotificationCenter;
use ILIAS\UI\Component\Component;
use ILIAS\UI\Component\MainControls\Slate\Combined;

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
 * Class NotificationCenterRenderer
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class NotificationCenterRenderer extends AbstractMetaBarItemRenderer implements MetaBarItemRenderer
{
    use isSupportedTrait;
    
    private \ILIAS\GlobalScreen\Services $gs;
    
    private \ilLanguage $lng;
    
    /**
     * BaseMetaBarItemRenderer constructor.
     */
    public function __construct()
    {
        global $DIC;
        $this->gs = $DIC->globalScreen();
        $this->lng = $DIC->language();
        parent::__construct();
    }
    
    /**
     * @param NotificationCenter $item
     * @return Component
     */
    protected function getSpecificComponentForItem(isItem $item) : Component
    {
        $f = $this->ui->factory();
        
        $center = $f->mainControls()->slate()->combined($this->lng->txt("noc"), $item->getSymbol())
                    ->withEngaged(false);
        
        foreach ($this->gs->collector()->notifications()->getNotifications() as $notification) {
            $center = $center->withAdditionalEntry($notification->getRenderer($this->ui->factory())->getNotificationComponentForItem($notification));
        }
        
        return $this->attachJSShowEvent($center);
    }
    
    /**
     * Attaches on load code for communicating back, that the notification
     * center has been opened. This allows to take measures needed to be
     * handled, if the notifications in the center have been consulted.
     * @param Combined $center
     * @return \ILIAS\UI\Component\JavaScriptBindable|Combined
     */
    protected function attachJSShowEvent(Combined $center) : \ILIAS\UI\Component\MainControls\Slate\Combined
    {
        $toggle_signal = $center->getToggleSignal();
        $url = ClientNotifications::NOTIFY_ENDPOINT . "?" . $this->buildShowQuery();
        
        $center = $center->withAdditionalOnLoadCode(
            function ($id) use ($toggle_signal, $url) {
                return "
                $(document).on('$toggle_signal', function(event, signalData) {
                    $.ajax({url: '$url'});
                });";
            }
        );
        
        return $center;
    }
    
    /**
     * @return string
     */
    protected function buildShowQuery() : string
    {
        return http_build_query([
            ClientNotifications::MODE => ClientNotifications::MODE_OPENED,
            ClientNotifications::NOTIFICATION_IDENTIFIERS => $this->gs->collector()->notifications()->getNotificationsIdentifiersAsArray(true),
        ]);
    }
}
