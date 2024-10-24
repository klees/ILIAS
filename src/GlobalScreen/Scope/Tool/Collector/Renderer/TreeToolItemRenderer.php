<?php

namespace ILIAS\GlobalScreen\Scope\Tool\Collector\Renderer;

use ILIAS\GlobalScreen\Scope\MainMenu\Collector\Renderer\BaseTypeRenderer;
use ILIAS\GlobalScreen\Scope\MainMenu\Factory\isItem;
use ILIAS\GlobalScreen\Scope\Tool\Factory\TreeTool;
use ILIAS\UI\Component\Component;

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
 * Class TreeToolItemRenderer
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class TreeToolItemRenderer extends BaseTypeRenderer
{
    
    /**
     * @param isItem $item
     * @param bool   $with_content
     * @return Component
     */
    public function getComponentForItem(isItem $item, bool $with_content = false) : Component
    {
        global $DIC;
        /**
         * @var $item TreeTool
         */
        
        $symbol = $this->getStandardSymbol($item);
        
        return $this->ui_factory->mainControls()->slate()->legacy($item->getTitle(), $symbol, $this->ui_factory->legacy($DIC->ui()->renderer()->render([$item->getTree()])));
    }
}
