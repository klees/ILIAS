<?php declare(strict_types=1);

namespace ILIAS\ResourceStorage\Resource\InfoResolver;

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
 * Class AbstractInfoResolver
 * @package ILIAS\ResourceStorage\Resource\InfoResolver
 * @internal
 */
abstract class AbstractInfoResolver implements InfoResolver
{
    protected int $revision_owner_id = 0;
    protected string $revision_title = '';
    protected int $next_version_number = 0;

    /**
     * AbstractInfoResolver constructor.
     */
    public function __construct(int $next_version_number, int $revision_owner_id, string $revision_title)
    {
        $this->next_version_number = $next_version_number;
        $this->revision_owner_id = $revision_owner_id;
        $this->revision_title = $revision_title;
    }

    public function getNextVersionNumber() : int
    {
        return $this->next_version_number;
    }

    public function getOwnerId() : int
    {
        return $this->revision_owner_id;
    }

    public function getRevisionTitle() : string
    {
        return $this->revision_title;
    }

}
