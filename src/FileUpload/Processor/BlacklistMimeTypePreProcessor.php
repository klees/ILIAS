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
 * Class BlacklistMimeTypePreProcessor
 *
 * Blacklist processor.
 * The processor drops all files which have one of the listed mime types.
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 * @since 5.3
 * @version 1.0.0
 *
 * @public
 */
final class BlacklistMimeTypePreProcessor implements PreProcessor
{
    /**
     * @var string[] $blacklist
     */
    private array $blacklist;


    /**
     * BlacklistMimeTypePreProcessor constructor.
     *
     * Blacklist example:
     * ['audio/aiff', 'application/javascript']
     *
     * @param string[] $blacklist           The list of mime types which should be filtered.
     *
     * @throws \InvalidArgumentException    Thrown if the supplied blacklist is empty.
     */
    public function __construct(array $blacklist)
    {
        if ($blacklist === []) {
            throw new \InvalidArgumentException('Blacklist must not be empty.');
        }

        $this->validateListEntries($blacklist);

        $this->blacklist = $blacklist;
    }


    /**
     * @inheritDoc
     */
    public function process(FileStream $stream, Metadata $metadata) : ProcessingStatus
    {
        if ($this->isBlacklisted($metadata->getMimeType())) {
            return new ProcessingStatus(ProcessingStatus::REJECTED, 'The mime type ' . $metadata->getMimeType() . ' is blacklisted.');
        }
        
        return new ProcessingStatus(ProcessingStatus::OK, 'Entity comply with mime type blacklist.');
    }


    /**
     * Checks if the supplied mime type is blacklisted.
     *
     * @param string $mimeType      The mime type which should be checked.
     *
     * @return bool                 True if the mime type is blacklisted otherwise false.
     */
    private function isBlacklisted(string $mimeType) : bool
    {
        foreach ($this->blacklist as $entry) {
            $entryJunks = explode('/', $entry);
            $mimeTypeJunks = explode('/', $mimeType);

            if ((strcmp($entryJunks[0], $mimeTypeJunks[0]) === 0 || strcmp($entryJunks[0], '*') === 0)
                && (strcmp($entryJunks[1], $mimeTypeJunks[1]) === 0 || strcmp($entryJunks[1], '*') === 0)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Checks if the supplied list contains invalid filters.
     * This method takes no further actions if the supplied list is valid.
     *
     * @param string[] $list    The list which should be validated.
     *
     * @throws \InvalidArgumentException Thrown if the list contains invalid list items.
     */
    private function validateListEntries(array $list) : void
    {
        if (in_array('*/*', $list, true)) {
            throw new \InvalidArgumentException('The mime type */* matches all mime types which would black all files.');
        }
    }
}
