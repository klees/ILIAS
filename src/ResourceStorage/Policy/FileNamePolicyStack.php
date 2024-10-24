<?php

namespace ILIAS\ResourceStorage\Policy;

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
 * Class FileNamePolicyStack
 * @author Fabian Schmid <fs@studer-raimann.ch>
 * @internal
 */
class FileNamePolicyStack implements FileNamePolicy
{
    /**
     * @var FileNamePolicy[]
     */
    protected array $stack = [];

    public function addPolicy(FileNamePolicy $policy) : void
    {
        $this->stack[] = $policy;
    }

    public function isValidExtension(string $extension) : bool
    {
        foreach ($this->stack as $policy) {
            if (!$policy->isValidExtension($extension)) {
                return false;
            }
        }
        return true;
    }

    public function isBlockedExtension(string $extension) : bool
    {
        foreach ($this->stack as $policy) {
            if (!$policy->isBlockedExtension($extension)) {
                return false;
            }
        }
        return true;
    }

    public function prepareFileNameForConsumer(string $filename_with_extension) : string
    {
        foreach ($this->stack as $policy) {
            $filename_with_extension = $policy->prepareFileNameForConsumer($filename_with_extension);
        }
        return $filename_with_extension;
    }

    public function check(string $extension) : bool
    {
        foreach ($this->stack as $policy) {
            $policy->check($extension);
        }
        return true;
    }

}
