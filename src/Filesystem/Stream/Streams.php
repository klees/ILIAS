<?php
declare(strict_types=1);

namespace ILIAS\Filesystem\Stream;

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
 * Class Streams
 *
 * Stream factory which enables the user to create streams without the knowledge of the concrete class.
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 * @since 5.3
 * @version 1.1.0
 *
 * @public
 */
final class Streams
{

    /**
     * Creates a new stream with an initial value.
     * Please note that the whole stream is stored within memory.
     *
     * @param string $string The string which should be written as initial value.
     *
     * @return FileStream The newly created in memory stream.
     */
    public static function ofString(string $string) : \ILIAS\Filesystem\Stream\Stream
    {
        if (!is_string($string)) {
            throw new \InvalidArgumentException('The argument $string must be of type string but was "' . gettype($string) . '"');
        }

        $stream = new Stream(fopen('php://memory', 'rw'));
        $stream->write($string);
        return $stream;
    }


    /**
     * Wraps an already created resource with the stream abstraction.
     * The stream abstraction only supports streams which are opened with fopen.
     *
     * @param resource $resource The resource which should be wrapped.
     *
     * @return FileStream The newly created stream which wraps the given resource.
     *
     * @see fopen()
     */
    public static function ofResource($resource) : \ILIAS\Filesystem\Stream\Stream
    {
        if (!is_resource($resource)) {
            throw new \InvalidArgumentException('The argument $resource must be of type resource but was "' . gettype($resource) . '"');
        }

        return new Stream($resource);
    }


    /**
     * Create a FileStream from a Psr7 compliant stream.
     * Please not that the stream must be detached from the psr7 stream in order to create the filesystem stream.
     *
     * @param StreamInterface $stream   The stream which should be parsed into a FileStream.
     * @return FileStream               The newly created stream.
     */
    public static function ofPsr7Stream(StreamInterface $stream) : \ILIAS\Filesystem\Stream\Stream
    {
        $resource = $stream->detach();
        return self::ofResource($resource);
    }

    public function ofZipResource(\ZipArchive $zip, int $index) : void
    {

    }

}
