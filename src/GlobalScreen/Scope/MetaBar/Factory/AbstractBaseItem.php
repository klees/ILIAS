<?php namespace ILIAS\GlobalScreen\Scope\MetaBar\Factory;

use ILIAS\GlobalScreen\Identification\IdentificationInterface;
use ILIAS\GlobalScreen\Scope\ComponentDecoratorTrait;
use ILIAS\GlobalScreen\Scope\MetaBar\Collector\Renderer\BaseMetaBarItemRenderer;
use ILIAS\GlobalScreen\Scope\MetaBar\Collector\Renderer\MetaBarItemRenderer;

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
 * Class AbstractBaseItem
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
abstract class AbstractBaseItem implements isItem
{
    use ComponentDecoratorTrait;
    
    protected MetaBarItemRenderer $renderer;
    protected int $position = 0;
    protected ?\Closure $available_callable = null;
    protected IdentificationInterface $provider_identification;
    protected ?\Closure $visiblility_callable = null;
    
    /**
     * AbstractBaseItem constructor.
     * @param IdentificationInterface $provider_identification
     */
    public function __construct(IdentificationInterface $provider_identification)
    {
        $this->provider_identification = $provider_identification;
        $this->renderer = new BaseMetaBarItemRenderer();
    }
    
    /**
     * @inheritDoc
     */
    public function getRenderer() : MetaBarItemRenderer
    {
        return $this->renderer;
    }
    
    /**
     * @inheritDoc
     */
    public function getProviderIdentification() : IdentificationInterface
    {
        return $this->provider_identification;
    }
    
    /**
     * @inheritDoc
     */
    public function withVisibilityCallable(callable $is_visible) : isItem
    {
        $clone = clone($this);
        $clone->visiblility_callable = $is_visible;
        
        return $clone;
    }
    
    /**
     * @inheritDoc
     */
    public function isVisible() : bool
    {
        if (!$this->isAvailable()) {
            return false;
        }
        if (is_callable($this->visiblility_callable)) {
            $callable = $this->visiblility_callable;
            
            $value = $callable();
            
            return $value;
        }
        
        return true;
    }
    
    /**
     * @inheritDoc
     */
    public function withAvailableCallable(callable $is_available) : isItem
    {
        $clone = clone($this);
        $clone->available_callable = $is_available;
        
        return $clone;
    }
    
    /**
     * @inheritDoc
     */
    public function isAvailable() : bool
    {
        if (is_callable($this->available_callable)) {
            $callable = $this->available_callable;
            
            $value = $callable();
            
            return $value;
        }
        
        return true;
    }
    
    /**
     * @inheritDoc
     */
    public function getPosition() : int
    {
        return $this->position;
    }
    
    /**
     * @inheritDoc
     */
    public function withPosition(int $position) : isItem
    {
        $clone = clone($this);
        $clone->position = $position;
        
        return $clone;
    }
}
