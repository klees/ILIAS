<?php namespace ILIAS\GlobalScreen\Scope\MainMenu\Collector;

use ILIAS\GlobalScreen\Collector\AbstractBaseCollector;
use ILIAS\GlobalScreen\Collector\ItemCollector;
use ILIAS\GlobalScreen\Identification\IdentificationInterface;
use ILIAS\GlobalScreen\Provider\Provider;
use ILIAS\GlobalScreen\Scope\MainMenu\Collector\Handler\BaseTypeHandler;
use ILIAS\GlobalScreen\Scope\MainMenu\Collector\Handler\TypeHandler;
use ILIAS\GlobalScreen\Scope\MainMenu\Collector\Information\ItemInformation;
use ILIAS\GlobalScreen\Scope\MainMenu\Collector\Information\TypeInformation;
use ILIAS\GlobalScreen\Scope\MainMenu\Collector\Information\TypeInformationCollection;
use ILIAS\GlobalScreen\Scope\MainMenu\Collector\Map\Map;
use ILIAS\GlobalScreen\Scope\MainMenu\Factory\hasSymbol;
use ILIAS\GlobalScreen\Scope\MainMenu\Factory\hasTitle;
use ILIAS\GlobalScreen\Scope\MainMenu\Factory\isChild;
use ILIAS\GlobalScreen\Scope\MainMenu\Factory\isInterchangeableItem;
use ILIAS\GlobalScreen\Scope\MainMenu\Factory\isItem;
use ILIAS\GlobalScreen\Scope\MainMenu\Factory\isParent;
use ILIAS\GlobalScreen\Scope\MainMenu\Factory\isTopItem;
use ILIAS\GlobalScreen\Scope\MainMenu\Factory\supportsAsynchronousLoading;
use ILIAS\GlobalScreen\Scope\MainMenu\Provider\StaticMainMenuProvider;
use ILIAS\GlobalScreen\Scope\MainMenu\Factory\Item\Lost;
use ILIAS\GlobalScreen\Scope\MainMenu\Factory\MainMenuItemFactory;

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
 * Class MainMenuMainCollector
 * This Collector will collect and then provide all available slates from the
 * providers in the whole system, stack them and enrich them will their content
 * based on the configuration in "Administration".
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class MainMenuMainCollector extends AbstractBaseCollector implements ItemCollector
{
    private TypeInformationCollection $type_information_collection;
    private ?ItemInformation $information;
    /**
     * @var Provider[]
     */
    protected array $providers;
    private Map $map;
    
    /**
     * MainMenuMainCollector constructor.
     * @param array                $providers
     * @param ItemInformation|null $information
     * @throws \Throwable
     */
    public function __construct(array $providers, MainMenuItemFactory $factory, ItemInformation $information = null)
    {
        $this->information = $information;
        $this->providers = $providers;
        $this->type_information_collection = new TypeInformationCollection();
        $this->map = new Map($factory);
    }
    
    /**
     * @return \Iterator<\ILIAS\GlobalScreen\Provider\Provider[]>
     */
    private function getProvidersFromList() : \Iterator
    {
        yield from $this->providers;
    }
    
    public function collectStructure() : void
    {
        foreach ($this->getProvidersFromList() as $provider) {
            $this->type_information_collection->append($provider->provideTypeInformation());
            $this->map->addMultiple(...$provider->getStaticTopItems());
            $this->map->addMultiple(...$provider->getStaticSubItems());
        }
    }
    
    public function filterItemsByVisibilty(bool $async_only = false) : void
    {
        // apply filter
        $this->map->filter(function (isItem $item) use ($async_only) : bool {
            if ($async_only && !$item instanceof supportsAsynchronousLoading) {
                return false;
            }
            if (!$item->isAvailable()) {
                return false;
            }
            
            // make parent available if one child is always available
            if ($item instanceof isParent) {
                foreach ($item->getChildren() as $child) {
                    if ($child->isAlwaysAvailable()) {
                        return true;
                    }
                }
            }
            
            // Always avaiable must be delivered when visible
            if ($item->isAlwaysAvailable()) {
                return $item->isVisible();
            }
            // All other cases
            return $item->isAvailable() && $item->isVisible() && $this->information->isItemActive($item);
        });
    }
    
    public function prepareItemsForUIRepresentation() : void
    {
        $this->map->walk(function (isItem &$item) : isItem {
            if (is_null($item->getTypeInformation())) {
                $item->setTypeInformation(
                    $this->getTypeInformationForItem($item)
                );
            }
            
            // Apply the TypeHandler
            $item = $this->getTypeHandlerForItem($item)->enrichItem($item);
            // Translate Item
            if ($item instanceof hasTitle) {
                $item = $this->getItemInformation()->customTranslationForUser($item);
            }
            // Custom Symbol
            if ($item instanceof hasSymbol) {
                $item = $this->getItemInformation()->customSymbol($item);
            }
            // Custom Position
            $item = $this->getItemInformation()->customPosition($item);
            
            return $item;
        });
        
        // Override parent from configuration
        $this->map->walk(function (isItem &$item) : \ILIAS\GlobalScreen\Scope\MainMenu\Factory\isItem {
            if ($item instanceof isChild) {
                $parent = $this->map->getSingleItemFromFilter($this->information->getParent($item));
                if ($parent instanceof isParent) {
                    $parent->appendChild($item);
                    if ($parent instanceof Lost && $parent->getProviderIdentification()->serialize() === '') {
                        $item->overrideParent($parent->getProviderIdentification());
                    }
                }
            }
            
            return $item;
        });
    }
    
    public function cleanupItemsForUIRepresentation() : void
    {
        // Remove not visible children
        $this->map->walk(function (isItem &$item) : isItem {
            if ($item instanceof isParent) {
                foreach ($item->getChildren() as $child) {
                    if (!$this->map->existsInFilter($child->getProviderIdentification())) {
                        $item->removeChild($child);
                    }
                }
            }
            return $item;
        });
    
        $this->map->walk(static function (isItem &$i) : void {
            if ($i instanceof isParent && count($i->getChildren()) === 0) {
                $i = $i->withAvailableCallable(static function () {
                    return false;
                })->withVisibilityCallable(static function () {
                    return false;
                });
            }
        });
        
        // filter empty slates
        $this->map->filter(static function (isItem $i) : bool {
            if ($i instanceof isParent) {
                return count($i->getChildren()) > 0;
            }
            
            return true;
        });
     
    }
    
    public function sortItemsForUIRepresentation() : void
    {
        $this->map->sort();
    }
    
    /**
     * This will return all available isTopItem (and moved isInterchangeableItem),
     * stacked based on the configuration in "Administration" and for the
     * visibility of the currently user.
     * @return \Generator|isTopItem[]|isInterchangeableItem[]
     */
    public function getItemsForUIRepresentation() : \Generator
    {
        foreach ($this->map->getAllFromFilter() as $item) {
            if ($item->isTop()) {
                yield $item;
            }
        }
    }
    
    /**
     * @return \Iterator<\Generator&\ILIAS\GlobalScreen\Scope\MainMenu\Factory\isItem[]>
     */
    public function getRawItems() : \Iterator
    {
        yield from $this->map->getAllFromFilter();
    }
    
    /**
     * @inheritDoc
     */
    public function hasItems() : bool
    {
        return $this->map->has();
    }
    
    /**
     * @param IdentificationInterface $identification
     * @return isItem
     * @deprecated
     */
    public function getSingleItemFromFilter(IdentificationInterface $identification) : isItem
    {
        return $this->map->getSingleItemFromFilter($identification);
    }
    
    /**
     * @param IdentificationInterface $identification
     * @return isItem
     * @deprecated
     */
    public function getSingleItemFromRaw(IdentificationInterface $identification) : isItem
    {
        return $this->map->getSingleItemFromRaw($identification);
    }
    
    /**
     * @param isItem $item
     * @return TypeHandler
     */
    public function getTypeHandlerForItem(isItem $item) : TypeHandler
    {
        $type_information = $this->getTypeInformationForItem($item);
        if ($type_information === null) {
            return new BaseTypeHandler();
        }
        
        return $type_information->getTypeHandler();
    }
    
    /**
     * @param isItem $item
     */
    public function getItemInformation() : ?\ILIAS\GlobalScreen\Scope\MainMenu\Collector\Information\ItemInformation
    {
        return $this->information;
    }
    
    /**
     * @param isItem $item
     * @return TypeInformation
     */
    public function getTypeInformationForItem(isItem $item) : TypeInformation
    {
        return $this->getTypeInformationCollection()->get(get_class($item));
    }
    
    /**
     * @return TypeInformationCollection
     */
    public function getTypeInformationCollection() : TypeInformationCollection
    {
        return $this->type_information_collection;
    }
}
