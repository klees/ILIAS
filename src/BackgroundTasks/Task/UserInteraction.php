<?php

namespace ILIAS\BackgroundTasks\Task;

use ILIAS\BackgroundTasks\Bucket;
use ILIAS\BackgroundTasks\Task;
use ILIAS\BackgroundTasks\Task\UserInteraction\Option;
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
 * Interface UserInteraction
 * @package ILIAS\BackgroundTasks\Task
 *          A Task in the Bucket, which will need some User-Interaction before running the task.
 */
interface UserInteraction extends Task
{
    
    /**
     * @param Value[] $input The input value of this task.
     * @return Option[] Options are buttons the user can press on this interaction.
     */
    public function getOptions(array $input) : array;
    
    /**
     * Decide whether the UserInteraction is presented to the user and he has to decide
     * or user UserInteraction is skipped by the TaskManager. You must return a valid
     * Value then, @param Value[] $input The input value of this task.
     * @see getSkippedValue.
     */
    public function canBeSkipped(array $input) : bool;
    
    /**
     * @param Value[] $input
     * @see                        canBeSkipped, whenever you decide to skip the UserInteraction, you have to
     *                             return a valid Value to proceed.
     */
    public function getSkippedValue(array $input) : Value;
    
    /**
     * @return bool true if this is the last skippable interaction in your chain/bucket, defaults to true.
     */
    public function isFinal() : bool;
    
    /**
     * @param \ILIAS\BackgroundTasks\Value[] $input                The input value of this task.
     * @param Option                         $user_selected_option The Option the user chose.
     * @param Bucket                         $bucket               Notify the bucket about your
     *                                                             progress!
     */
    public function interaction(array $input, Option $user_selected_option, Bucket $bucket) : Value;
    
    /**
     * @param Value[] $input The input value of this task.
     * @return string $message enables the UserInteraction to be used as a notification (for example when a bucket fails due to an
     *                       expected condition not being met)
     */
    public function getMessage(array $input) : string;
}
