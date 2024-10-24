<?php declare(strict_types=1);

namespace ILIAS\Contact\Provider;

use ilBuddySystem;
use ILIAS\GlobalScreen\Scope\MainMenu\Provider\AbstractStaticMainMenuProvider;
use ILIAS\MainMenu\Provider\StandardTopItemsProvider;
use ILIAS\UI\Component\Symbol\Icon\Standard;

/**
 * Class ContactMainBarProvider
 *
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class ContactMainBarProvider extends AbstractStaticMainMenuProvider
{

    /**
     * @inheritDoc
     */
    public function getStaticTopItems() : array
    {
        return [];
    }


    /**
     * @inheritDoc
     */
    public function getStaticSubItems() : array
    {
        $title = $this->dic->language()->txt("mm_contacts");

        $icon = $this->dic->ui()->factory()
            ->symbol()
            ->icon()
            ->standard(Standard::CADM, 'contacts')->withIsOutlined(true);

        return [
            $this->mainmenu->link($this->if->identifier('mm_pd_contacts'))
                ->withTitle($title)
                ->withAction("ilias.php?baseClass=ilDashboardGUI&cmd=jumpToContacts")
                ->withParent(StandardTopItemsProvider::getInstance()->getCommunicationIdentification())
                ->withPosition(20)
                ->withSymbol($icon)
                ->withNonAvailableReason($this->dic->ui()->factory()->legacy($this->dic->language()->txt('component_not_active')))
                ->withAvailableCallable(
                    static function () : bool {
                        return ilBuddySystem::getInstance()->isEnabled();
                    }
                ),
        ];
    }
}
