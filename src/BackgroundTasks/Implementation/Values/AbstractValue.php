<?php

namespace ILIAS\BackgroundTasks\Implementation\Values;

use ILIAS\BackgroundTasks\Task;
use ILIAS\BackgroundTasks\Types\SingleType;
use ILIAS\BackgroundTasks\Types\Type;
use ILIAS\BackgroundTasks\Value;

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
 * Class AbstractValue
 * @package ILIAS\BackgroundTasks\Implementation\Values
 * @author  Oskar Truffer <ot@studer-raimann.ch>
 */
abstract class AbstractValue implements Value
{
    protected Task $parentTask;
    
    public function getType() : Type
    {
        return new SingleType(static::class);
    }
    
    public function getParentTask() : Task
    {
        return $this->parentTask;
    }
    
    public function setParentTask(Task $parentTask) : void
    {
        $this->parentTask = $parentTask;
    }
    
    public function hasParentTask() : bool
    {
        return isset($this->parentTask);
    }
}
