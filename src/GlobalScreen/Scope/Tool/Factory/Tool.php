<?php declare(strict_types=1);

namespace ILIAS\GlobalScreen\Scope\Tool\Factory;

use Closure;
use ILIAS\GlobalScreen\Scope\MainMenu\Factory\hasContent;
use ILIAS\GlobalScreen\Scope\MainMenu\Factory\hasSymbol;
use ILIAS\GlobalScreen\Scope\MainMenu\Factory\hasTitle;
use ILIAS\GlobalScreen\Scope\MainMenu\Factory\isTopItem;
use ILIAS\UI\Component\Component;
use ILIAS\UI\Component\Symbol\Glyph;
use ILIAS\UI\Component\Symbol\Icon;
use ILIAS\UI\Component\Symbol\Symbol;

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
 * Class Tool
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class Tool extends AbstractBaseTool implements isTopItem, hasContent, supportsTerminating
{
    protected string $title;
    protected ?Closure $terminated_callback = null;
    protected ?Symbol $symbol = null;
    protected ?Component $content = null;
    protected ?Closure $content_wrapper = null;
    protected ?Closure $close_callback = null;
    
    /**
     * @param string $title
     * @return Tool
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
    public function withContentWrapper(Closure $content_wrapper) : hasContent
    {
        $clone = clone($this);
        $clone->content_wrapper = $content_wrapper;
        
        return $clone;
    }
    
    /**
     * @inheritDoc
     */
    public function withContent(Component $ui_component) : hasContent
    {
        $clone = clone($this);
        $clone->content = $ui_component;
        
        return $clone;
    }
    
    /**
     * @inheritDoc
     */
    public function getContent() : Component
    {
        if ($this->content_wrapper !== null) {
            $wrapper = $this->content_wrapper;
            
            return $wrapper();
        }
        
        return $this->content;
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
    
    /**
     * @inheritDoc
     */
    public function withTerminatedCallback(Closure $callback) : supportsTerminating
    {
        $clone = clone $this;
        $clone->terminated_callback = $callback;
        
        return $clone;
    }
    
    /**
     * @return Closure|null
     */
    public function getTerminatedCallback() : ?Closure
    {
        return $this->terminated_callback;
    }
    
    /**
     * @return bool
     */
    public function hasTerminatedCallback() : bool
    {
        return $this->terminated_callback instanceof Closure;
    }
}
