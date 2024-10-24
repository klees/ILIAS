<?php

use ILIAS\GlobalScreen\Identification\IdentificationInterface;
use ILIAS\GlobalScreen\Scope\MainMenu\Collector\MainMenuMainCollector as Main;
use ILIAS\MainMenu\Provider\CustomMainBarProvider;

/**
 * Class ilMMNullItemFacade
 *
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class ilMMNullItemFacade extends ilMMCustomItemFacade implements ilMMItemFacadeInterface
{
    
    private ?string $parent_identification = "";
    private bool $active_status;
    protected bool $top_item = false;


    /**
     * @inheritDoc
     */
    public function __construct(IdentificationInterface $identification, Main $collector)
    {
        $this->identification = $identification;
        parent::__construct($identification, $collector);
    }


    /**
     * @inheritDoc
     */
    public function isTopItem() : bool
    {
        return $this->top_item;
    }


    /**
     * @inheritDoc
     */
    public function setIsTopItm(bool $top_item) : void
    {
        $this->top_item = $top_item;
    }


    /**
     * @inheritDoc
     */
    public function isEmpty() : bool
    {
        return true;
    }


    /**
     * @inheritDoc
     */
    public function setActiveStatus(bool $status) : void
    {
        $this->active_status = $status;
    }


    /**
     * @inheritDoc
     */
    public function setParent(string $parent) : void
    {
        $this->parent_identification = $parent;
    }


    public function create() : void
    {
        $s = new ilMMCustomItemStorage();
        $s->setIdentifier(uniqid());
        $s->setType($this->type);
        $s->setTopItem($this->isTopItem());
        $s->setAction($this->action);
        $s->setDefaultTitle($this->default_title);
        $s->create();

        $this->custom_item_storage = $s;

        global $DIC;
        $provider = new CustomMainBarProvider($DIC);
        $this->raw_item = $provider->getSingleCustomItem($s);
        if ($this->parent_identification && $this->raw_item instanceof \ILIAS\GlobalScreen\Scope\MainMenu\Factory\isChild) {
            global $DIC;
            $this->raw_item = $this->raw_item->withParent($DIC->globalScreen()->identification()->fromSerializedIdentification($this->parent_identification));
        }

        $this->identification = $this->raw_item->getProviderIdentification();

        $this->mm_item = new ilMMItemStorage();
        $this->mm_item->setPosition(9999999); // always the last on the top item
        $this->mm_item->setIdentification($this->raw_item->getProviderIdentification()->serialize());
        $this->mm_item->setParentIdentification($this->parent_identification);
        $this->mm_item->setActive($this->active_status);
        if ($this->raw_item instanceof \ILIAS\GlobalScreen\Scope\MainMenu\Factory\isChild) {
            $this->mm_item->setParentIdentification($this->raw_item->getParent()->serialize());
        }

        parent::create();
    }


    public function isAvailable() : bool
    {
        return false;
    }


    /**
     * @inheritDoc
     */
    public function isAlwaysAvailable() : bool
    {
        return false;
    }


    /**
     * @inheritDoc
     */
    public function getProviderNameForPresentation() : string
    {
        return $this->identification->getProviderNameForPresentation();
    }


    /**
     * @inheritDoc
     */
    public function isDeletable() : bool
    {
        return true;
    }


    /**
     * @inheritDoc
     */
    public function delete() : void
    {
        parent::delete();
    }

    /**
     * @inheritDoc
     */
    public function supportsRoleBasedVisibility() : bool
    {
        return true;
    }

}
