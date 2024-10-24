<?php namespace ILIAS\GlobalScreen\Scope\Layout\Factory;

use ILIAS\GlobalScreen\Scope\Layout\Provider\PagePart\PagePartProvider;
use ILIAS\UI\Component\Layout\Page\Page;

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
 * Class PageBuilderModification
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class PageBuilderModification extends AbstractLayoutModification implements LayoutModification
{
    
    /**
     * @inheritDoc
     */
    public function firstArgumentAllowsNull() : bool
    {
        return false;
    }
    
    /**
     * @inheritDoc
     */
    public function returnTypeAllowsNull() : bool
    {
        return false;
    }
    
    /**
     * @inheritDoc
     */
    public function isFinal() : bool
    {
        return true;
    }
    
    /**
     * @inheritDoc
     */
    public function getClosureFirstArgumentType() : string
    {
        return PagePartProvider::class;
    }
    
    /**
     * @inheritDoc
     */
    public function getClosureReturnType() : string
    {
        return Page::class;
    }
}
