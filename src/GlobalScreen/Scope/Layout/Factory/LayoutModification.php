<?php namespace ILIAS\GlobalScreen\Scope\Layout\Factory;

use Closure;
use LogicException;

/**
 * Class LayoutModification
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
interface LayoutModification
{
    public const PRIORITY_LOW = 2;
    public const PRIORITY_HIGH = 64;
    
    /**
     * @return int (LayoutModification::PRIORITY_LOW|LayoutModification::PRIORITY_HIGH)
     */
    public function getPriority() : int;
    
    /**
     * @param int $priority (LayoutModification::PRIORITY_LOW|LayoutModification::PRIORITY_HIGH)
     * @return LayoutModification|ContentModification|MainBarModification|MetaBarModification|BreadCrumbsModification|LogoModification|FooterModification
     * @throws LogicException if not LayoutModification::PRIORITY_LOW|LayoutModification::PRIORITY_HIGH
     */
    public function withPriority(int $priority) : LayoutModification;
    
    /**
     * @return LayoutModification|ContentModification|MainBarModification|MetaBarModification|BreadCrumbsModification|LogoModification|FooterModification
     */
    public function withHighPriority() : LayoutModification;
    
    /**
     * @return LayoutModification|ContentModification|MainBarModification|MetaBarModification|BreadCrumbsModification|LogoModification|FooterModification
     */
    public function withLowPriority() : LayoutModification;
    
    /**
     * @return bool
     * @deprecated
     */
    public function isFinal() : bool;
    
    /**
     * @param Closure $closure
     * @return LayoutModification|ContentModification|MainBarModification|MetaBarModification|BreadCrumbsModification|LogoModification|FooterModification
     */
    public function withModification(Closure $closure) : LayoutModification;
    
    /**
     * @return bool
     */
    public function hasValidModification() : bool;
    
    /**
     * @return Closure
     */
    public function getModification() : Closure;
    
    /**
     * @return string|null
     */
    public function getClosureFirstArgumentType() : string;
    
    /**
     * @return string
     */
    public function getClosureReturnType() : string;
    
    /**
     * @return bool
     */
    public function firstArgumentAllowsNull() : bool;
    
    /**
     * @return bool
     */
    public function returnTypeAllowsNull() : bool;
}
