<?php

namespace ILIAS\Filesystem\Provider;

use ILIAS\Filesystem\Exception\FileNotFoundException;
use ILIAS\Filesystem\Exception\IOException;
use ILIAS\Filesystem\Stream\FileStream;

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
 * Interface FileStreamReadAccess
 *
 * This interface describes all readonly streaming filesystem operations.
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 * @since 5.3
 * @version 1.0
 *
 * @see FileStreamAccess
 *
 * @public
 */
interface FileStreamReadAccess
{

    /**
     * Opens a readable stream of the file.
     * Please make sure to close the stream after the work is done with Stream::close()
     *
     * @param string $path  The path to the file which should be used to open the new stream.
     *
     * @return FileStream The newly created file stream.
     *
     * @throws FileNotFoundException    If the file could not be found.
     * @throws IOException              If the stream could not be opened.
     *
     * @since 5.3
     * @version 1.0
     *
     * @see FileStream::close()
     */
    public function readStream(string $path) : FileStream;
}
