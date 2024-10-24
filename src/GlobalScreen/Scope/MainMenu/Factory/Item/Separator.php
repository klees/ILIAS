<?php namespace ILIAS\GlobalScreen\Scope\MainMenu\Factory\Item;

use ILIAS\GlobalScreen\Scope\MainMenu\Factory\AbstractChildItem;
use ILIAS\GlobalScreen\Scope\MainMenu\Factory\hasTitle;
use ILIAS\GlobalScreen\Scope\MainMenu\Factory\isChild;

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
 * Class Separator
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class Separator extends AbstractChildItem implements hasTitle, isChild
{
    protected bool $visible_title = false;
    protected string $title = '';
    
    /**
     * @param string $title
     * @return Separator
     */
    public function withTitle(string $title) : hasTitle
    {
        $clone = clone($this);
        $clone->title = $title;
        
        return $clone;
    }
    
    /**
     * @return string
     */
    public function getTitle() : string
    {
        return $this->title;
    }
    
    public function withVisibleTitle(bool $visible_title) : self
    {
        $clone = clone($this);
        $clone->visible_title = $visible_title;
        
        return $clone;
    }
    
    /**
     * @return bool
     */
    public function isTitleVisible() : bool
    {
        return $this->visible_title;
    }
}
