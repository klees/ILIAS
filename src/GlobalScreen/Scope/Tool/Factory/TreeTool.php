<?php declare(strict_types=1);

namespace ILIAS\GlobalScreen\Scope\Tool\Factory;

use ILIAS\GlobalScreen\Scope\MainMenu\Factory\hasSymbol;
use ILIAS\GlobalScreen\Scope\MainMenu\Factory\hasTitle;
use ILIAS\GlobalScreen\Scope\MainMenu\Factory\isTopItem;
use ILIAS\GlobalScreen\Scope\MainMenu\Factory\SymbolDecoratorTrait;
use ILIAS\UI\Component\Symbol\Glyph;
use ILIAS\UI\Component\Symbol\Icon;
use ILIAS\UI\Component\Symbol\Symbol;
use ILIAS\UI\Component\Tree\Tree;

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
 * Class TreeTool
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class TreeTool extends AbstractBaseTool implements isTopItem, hasSymbol, isToolItem
{
    use SymbolDecoratorTrait;
    
    protected ?Symbol $symbol = null;
    protected Tree $tree;
    protected string $title;
    
    /**
     * @param string $title
     * @return TreeTool
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
    
    /**
     * @inheritDoc
     */
    public function withSymbol(Symbol $symbol) : hasSymbol
    {
        // bugfix mantis 25526: make aria labels mandatory
        if (($symbol instanceof Glyph\Glyph && $symbol->getAriaLabel() === "") ||
            ($symbol instanceof Icon\Icon && $symbol->getLabel() === "")) {
            throw new \LogicException("the symbol's aria label MUST be set to ensure accessibility");
        }
        
        $clone = clone($this);
        $clone->symbol = $symbol;
        
        return $clone;
    }
    
    /**
     * @inheritDoc
     */
    public function withTree(Tree $tree) : self
    {
        $clone = clone($this);
        $clone->tree = $tree;
        
        return $clone;
    }
    
    /**
     * @return Tree
     */
    public function getTree() : Tree
    {
        return $this->tree;
    }
    
    /**
     * @inheritDoc
     */
    public function getSymbol() : Symbol
    {
        return $this->symbol;
    }
    
    /**
     * @inheritDoc
     */
    public function hasSymbol() : bool
    {
        return ($this->symbol instanceof Symbol);
    }
}
