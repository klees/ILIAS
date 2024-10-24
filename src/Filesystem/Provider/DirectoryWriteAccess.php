<?php

namespace ILIAS\Filesystem\Provider;

use ILIAS\Filesystem\Exception\DirectoryNotFoundException;
use ILIAS\Filesystem\Exception\IOException;
use ILIAS\Filesystem\Visibility;

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
 * Interface DirectoryWriteAccess
 *
 * Defines the write operations of the directory access.
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 * @since 5.3
 * @version 1.0
 *
 * @see DirectoryAccess
 *
 * @public
 */
interface DirectoryWriteAccess
{

    /**
     * Create a new directory.
     *
     * Please note that the Visibility interface defines two constants PUBLIC_ACCESS and PRIVATE_ACCESS
     * to ease the development process.
     *
     * @param string $path          The directory path which should be created.
     * @param string $visibility    The visibility of the directory. Defaults to visibility public.
     *
     * @return void
     *
     * @throws IOException                  If the directory could not be created.
     * @throws \InvalidArgumentException     If the visibility is not 'public' or 'private'.
     *
     * @since 5.3
     * @version 1.0
     */
    public function createDir(string $path, string $visibility = Visibility::PUBLIC_ACCESS) : void;


    /**
     * Copy all childes of the source recursive to the destination.
     * The file access rights will be copied as well.
     *
     * The operation will fail fast if the destination directory is not empty.
     * All destination folders will be created if needed.
     *
     * @param string $source        The source which should be scanned and copied.
     * @param string $destination   The destination of the recursive copy.
     *
     * @return void
     *
     * @throws DirectoryNotFoundException   Thrown if the source directory could not be found.
     *
     * @throws IOException                  Thrown if the directory could not be copied.
     * @since 5.3
     * @version 1.0
     */
    public function copyDir(string $source, string $destination) : void;

    /**
     * Deletes a directory recursive.
     *
     * @param string $path  The path which should be deleted.
     *
     * @return void
     *
     * @throws IOException If the path could not be deleted.
     *
     * @since 5.3
     * @version 1.0
     */
    public function deleteDir(string $path) : void;
}
