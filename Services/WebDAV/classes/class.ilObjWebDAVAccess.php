<?php declare(strict_types = 1);

use ILIAS\HTTP\Services;
use ILIAS\HTTP\Wrapper\RequestWrapper;
use ILIAS\Refinery\Transformation;

/******************************************************************************
 *
 * This file is part of ILIAS, a powerful learning management system.
 *
 * ILIAS is licensed with the GPL-3.0, you should have received a copy
 * of said license along with the source code.
 *
 * If this is not the case or you just want to try ILIAS, you'll find
 * us at:
 * https://www.ilias.de
 * https://github.com/ILIAS-eLearning
 *
 *****************************************************************************/
/**
 * @author Lukas Zehnder <lz@studer-raimann.ch>
 */
class ilObjWebDAVAccess extends ilObjectAccess
{
    private RequestWrapper $http;
    private ilRbacSystem $rbacsystem;
    private Transformation $int_trafo;
    
    public function __construct()
    {
        global $DIC;
        $this->rbacsystem = $DIC->rbac()->system();
        $this->http = $DIC->http()->wrapper()->query();
        $this->int_trafo = $DIC->refinery()->kindlyTo()->int();
    }
    
    public function checkAccessAndThrowException(string $permission) : void
    {
        if (!$this->hasUserPermissionTo($permission)) {
            throw new ilException('Permission denied');
        }
    }
    
    public function hasUserPermissionTo(string $permission) : bool
    {
        if (!$this->http->has('ref_id')) {
            return false;
        }
        return (bool) $this->rbacsystem->checkAccess(
            $permission,
            $this->http->retrieve('ref_id', $this->int_trafo)
        );
    }
}
