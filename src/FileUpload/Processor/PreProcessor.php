<?php

namespace ILIAS\FileUpload\Processor;

use ILIAS\Filesystem\Stream\FileStream;
use ILIAS\FileUpload\DTO\Metadata;
use ILIAS\FileUpload\DTO\ProcessingStatus;
use Psr\Http\Message\StreamInterface;

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
 * Class PreProcessor
 *
 * The preprocessor interface defines the required methods for the preprocessors which are used to
 * process the file before it is moved.
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 * @since   5.3
 * @version 1.0
 *
 * @public
 */
interface PreProcessor
{

    /**
     * This method gets invoked by the file upload service to process the file with the help of the
     * processor. If the return value is REJECTED, no further invocations of processors are done
     * for the rejected file.
     *
     * If the processor fails or returns an unexpected value, the file gets automatically rejected
     * because the file could be dangerous to ILIAS.
     *
     * @param FileStream                 $stream   The stream of the file.
     * @param Metadata                   $metadata The meta data of the uploaded file.
     *
     * @return ProcessingStatus The new status of the file.
     */
    public function process(FileStream $stream, Metadata $metadata) : ProcessingStatus;
}
