<?php

namespace ILIAS\ResourceStorage\Consumer;

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
 * Interface DeliveryConsumer
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
interface DeliveryConsumer
{

    /**
     * This runs the actual DeliveryConsumer. E.g. a DownloadConsumer will pass the
     * Stream of a Ressource to the HTTP-Service and download the file.
     */
    public function run() : void;

    /**
     * @param int $revision_number of a specific revision. otherwise the latest
     *                             will be chosen during run()
     */
    public function setRevisionNumber(int $revision_number) : DeliveryConsumer;

    public function overrideFileName(string $file_name) : DeliveryConsumer;
}
