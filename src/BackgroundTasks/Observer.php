<?php

namespace ILIAS\BackgroundTasks;

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
interface Observer
{
    
    /**
     * If the bucket goes into another state notify the observer.
     * This also triggers a heartbeat.
     */
    public function notifyState(int $state) : void;
    
    /**
     * You can change the progress of a currently running task.
     * This also triggers a heartbeat.
     */
    public function notifyPercentage(Task $task, int $percentage) : void;
    
    /**
     * If the current task changes notify the observer.
     * This also triggers a heartbeat.
     */
    public function notifyCurrentTask(Task $task) : void;
    
    /**
     * I'm still alive! If your calculation takes a really long time don't forget to use the heartbeat. Otherwise
     * the bucket might be killed while still running. All notify tasks of the observer also trigger a heartbeat.
     */
    public function heartbeat() : void;
}
