<?php /** @noinspection PhpPropertyOnlyWrittenInspection */

namespace ILIAS\GlobalScreen\Scope\Tool\Collector;

use Closure;
use ILIAS\GlobalScreen\Collector\AbstractBaseCollector;
use ILIAS\GlobalScreen\Collector\ItemCollector;
use ILIAS\GlobalScreen\Scope\MainMenu\Collector\Handler\TypeHandler;
use ILIAS\GlobalScreen\Scope\MainMenu\Collector\Information\ItemInformation;
use ILIAS\GlobalScreen\Scope\MainMenu\Collector\Information\TypeInformation;
use ILIAS\GlobalScreen\Scope\MainMenu\Collector\Information\TypeInformationCollection;
use ILIAS\GlobalScreen\Scope\Tool\Collector\Renderer\ToolItemRenderer;
use ILIAS\GlobalScreen\Scope\Tool\Collector\Renderer\TreeToolItemRenderer;
use ILIAS\GlobalScreen\Scope\Tool\Factory\isToolItem;
use ILIAS\GlobalScreen\Scope\Tool\Factory\Tool;
use ILIAS\GlobalScreen\Scope\Tool\Factory\TreeTool;
use ILIAS\GlobalScreen\Scope\Tool\Provider\DynamicToolProvider;
use ILIAS\GlobalScreen\Identification\IdentificationInterface;

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
 * Class MainToolCollector
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class MainToolCollector extends AbstractBaseCollector implements ItemCollector
{
    private ?ItemInformation $information;
    private TypeInformationCollection $type_information_collection;
    /**
     * @var isToolItem[]
     */
    private array $tools;
    /**
     * @var DynamicToolProvider[]
     */
    private array $providers;
    
    /**
     * MainToolCollector constructor.
     * @param DynamicToolProvider[] $providers
     * @param ItemInformation|null  $information
     */
    public function __construct(array $providers, ItemInformation $information = null)
    {
        $this->providers = $providers;
        $this->information = $information;
        $this->type_information_collection = new TypeInformationCollection();
        
        // Tool
        $tool = new TypeInformation(Tool::class, Tool::class, new ToolItemRenderer());
        $tool->setCreationPrevented(true);
        $this->type_information_collection->add($tool);
        
        $tool = new TypeInformation(TreeTool::class, TreeTool::class, new TreeToolItemRenderer());
        $tool->setCreationPrevented(true);
        $this->type_information_collection->add($tool);
        
        $this->tools = [];
    }
    
    public function collectStructure() : void
    {
        global $DIC;
        $called_contexts = $DIC->globalScreen()->tool()->context()->stack();
        
        $tools_to_merge = [];
        
        foreach ($this->providers as $provider) {
            $context_collection = $provider->isInterestedInContexts();
            if ($context_collection->hasMatch($called_contexts)) {
                $tools_to_merge[] = $provider->getToolsForContextStack($called_contexts);
            }
        }
        $this->tools = array_merge([], ...$tools_to_merge);
    }
    
    public function filterItemsByVisibilty(bool $async_only = false) : void
    {
        $this->tools = array_filter($this->tools, $this->getVisibleFilter());
    }
    
    public function getSingleItem(IdentificationInterface $identification) : isToolItem
    {
        foreach ($this->tools as $tool) {
            if ($tool->getProviderIdentification()->serialize() === $identification->serialize()) {
                return $tool;
            }
        }
        return new Tool($identification);
    }
    
    public function prepareItemsForUIRepresentation() : void
    {
        array_walk($this->tools, function (isToolItem $tool) : void {
            $this->applyTypeInformation($tool);
        });
    }
    
    public function cleanupItemsForUIRepresentation() : void
    {
        // TODO: Implement cleanupItemsForUIRepresentation() method.
    }
    
    public function sortItemsForUIRepresentation() : void
    {
        usort($this->tools, $this->getItemSorter());
    }
    
    public function getItemsForUIRepresentation() : \Generator
    {
        yield from $this->tools;
    }
    
    /**
     * @return bool
     */
    public function hasItems() : bool
    {
        return count($this->tools) > 0;
    }
    
    /**
     * @param isToolItem $item
     * @return isToolItem
     */
    private function applyTypeInformation(isToolItem $item) : isToolItem
    {
        $item->setTypeInformation($this->getTypeInfoermationForItem($item));
        
        return $item;
    }
    
    /**
     * @param isToolItem $item
     * @return TypeInformation
     */
    private function getTypeInfoermationForItem(isToolItem $item) : TypeInformation
    {
        /**
         * @var $handler TypeHandler
         */
        $type = get_class($item);
        
        return $this->type_information_collection->get($type);
    }
    
    private function getVisibleFilter() : callable
    {
        return static function (isToolItem $tool) : bool {
            return ($tool->isAvailable() && $tool->isVisible());
        };
    }
    
    private function getItemSorter() : callable
    {
        return static function (isToolItem $a, isToolItem $b) : int {
            return $a->getPosition() - $b->getPosition();
        };
    }
}
