<?php declare(strict_types=1);
/* Copyright (c) 1998-2009 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * Description of class
 * @author  Stefan Meyer <meyer@leifos.com>
 * @ingroup ServicesDidacticTemplates
 */
class ilDidacticTemplateLocalPolicyAction extends ilDidacticTemplateAction
{
    public const TPL_ACTION_OVERWRITE = 1;
    public const TPL_ACTION_INTERSECT = 2;
    public const TPL_ACTION_ADD = 3;
    public const TPL_ACTION_SUBTRACT = 4;
    public const TPL_ACTION_UNION = 5;

    private array $pattern = [];
    private int $filter_type = self::FILTER_SOURCE_TITLE;
    private int $role_template_type = self::TPL_ACTION_OVERWRITE;
    private int $role_template_id = 0;

    /**
     * Constructor
     * @param int $action_id
     */
    public function __construct(int $action_id = 0)
    {
        global $DIC;

        parent::__construct($action_id);
    }

    /**
     * Add filter
     * @param ilDidacticTemplateFilterPattern $pattern
     */
    public function addFilterPattern(ilDidacticTemplateFilterPattern $pattern) : void
    {
        $this->pattern[] = $pattern;
    }

    /**
     * Set filter patterns
     * @param ilDidacticTemplateExcludeFilterPattern[] $patterns
     */
    public function setFilterPatterns(array $patterns) : void
    {
        $this->pattern = $patterns;
    }

    /**
     * Get filter pattern
     * @return ilDidacticTemplateFilterPattern[]
     */
    public function getFilterPattern() : array
    {
        return $this->pattern;
    }

    /**
     * Set filter type
     * @param int $a_type
     */
    public function setFilterType(int $a_type) : void
    {
        $this->filter_type = $a_type;
    }

    /**
     * Get filter type
     * @return int
     */
    public function getFilterType() : int
    {
        return $this->filter_type;
    }

    /**
     * Set Role template type
     * @param int $a_tpl_type
     */
    public function setRoleTemplateType(int $a_tpl_type) : void
    {
        $this->role_template_type = $a_tpl_type;
    }

    /**
     * Get role template type
     */
    public function getRoleTemplateType() : int
    {
        return $this->role_template_type;
    }

    /**
     * Set role template id
     * @param int $a_id
     */
    public function setRoleTemplateId(int $a_id) : void
    {
        $this->role_template_id = $a_id;
    }

    /**
     * Get role template id
     * @return int
     */
    public function getRoleTemplateId() : int
    {
        return $this->role_template_id;
    }

    /**
     * Save action
     */
    public function save() : int
    {
        if (!parent::save()) {
            return 0;
        }

        $query = 'INSERT INTO didactic_tpl_alp (action_id,filter_type,template_type,template_id) ' .
            'VALUES( ' .
            $this->db->quote($this->getActionId(), 'integer') . ', ' .
            $this->db->quote($this->getFilterType(), 'integer') . ', ' .
            $this->db->quote($this->getRoleTemplateType(), 'integer') . ', ' .
            $this->db->quote($this->getRoleTemplateId(), 'integer') . ' ' .
            ')';
        $this->db->manipulate($query);

        foreach ($this->getFilterPattern() as $pattern) {
            /* @var ilDidacticTemplateFilterPattern $pattern */
            $pattern->setParentId($this->getActionId());
            $pattern->setParentType(self::PATTERN_PARENT_TYPE);
            $pattern->save();
        }
        return $this->getActionId();
    }

    /**
     * delete action filter
     * @return void
     */
    public function delete() : void
    {
        parent::delete();
        $query = 'DELETE FROM didactic_tpl_alp ' .
            'WHERE action_id  = ' . $this->db->quote($this->getActionId(), 'integer');
        $this->db->manipulate($query);

        foreach ($this->getFilterPattern() as $pattern) {
            $pattern->delete();
        }
    }

    /**
     * Apply action
     */
    public function apply() : bool
    {
        $source = $this->initSourceObject();
        // Create a role folder for the new local policies
        $roles = $this->filterRoles($source);

        // Create local policy for filtered roles
        foreach ($roles as $role_id => $role) {
            $this->getLogger()->debug('Apply to role: ' . $role['title']);
            $this->getLogger()->debug('Role parent: ' . $role['parent']);
            $this->getLogger()->debug('Source ref_id: ' . $source->getRefId());

            // No local policies for protected roles of higher context
            if (
                $this->review->isProtected($role['parent'], $role_id) &&
                $role['parent'] != $source->getRefId()
            ) {
                $this->getLogger()->debug('Ignoring protected role.');
                continue;
            }
            $this->createLocalPolicy($source, $role);
        }
        return true;
    }

    /**
     * Revert action
     */
    public function revert() : bool
    {
        $source = $this->initSourceObject();
        $roles = $this->filterRoles($source);

        // Delete local policy for filtered roles
        foreach ($roles as $role_id => $role) {
            // Do not delete local policies of auto generated roles
            if (!$this->review->isGlobalRole($role['obj_id']) and
                $this->review->isAssignable($role['obj_id'], $source->getRefId()) and
                $this->review->isSystemGeneratedRole($role['obj_id'])) {
                $this->getLogger()->debug('Reverting local policy of auto generated role: ' . $role['title']);
                $this->revertLocalPolicy($source, $role);
            } else {
                $this->getLogger()->debug('Reverting local policy and deleting local role: ' . $role['title']);

                // delete local role and change exiting objects
                $this->admin->deleteLocalRole($role_id, $source->getRefId());

                // Change existing object
                $role_obj = new ilObjRole($role_id);

                $protected = $this->review->isProtected($role['parent'], $role['rol_id']);

                $role_obj->changeExistingObjects(
                    $source->getRefId(),
                    $protected ?
                        ilObjRole::MODE_PROTECTED_DELETE_LOCAL_POLICIES :
                        ilObjRole::MODE_UNPROTECTED_DELETE_LOCAL_POLICIES,
                    ['all']
                );
            }
        }
        return true;
    }

    public function getType() : int
    {
        return self::TYPE_LOCAL_POLICY;
    }

    /**
     * Export to xml
     * @param ilXmlWriter $writer
     * @return void
     */
    public function toXml(ilXmlWriter $writer) : void
    {
        $writer->xmlStartTag('localPolicyAction');

        switch ($this->getFilterType()) {
            case self::FILTER_SOURCE_TITLE:
                $writer->xmlStartTag('roleFilter', ['source' => 'title']);
                break;

            case self::FILTER_SOURCE_OBJ_ID:
                $writer->xmlStartTag('roleFilter', ['source' => 'objId']);
                break;

            case self::FILTER_PARENT_ROLES:
                $writer->xmlStartTag('roleFilter', ['source' => 'parentRoles']);
                break;

            case self::FILTER_LOCAL_ROLES:
                $writer->xmlStartTag('roleFilter', ['source' => 'localRoles']);
                break;

            default:
                $writer->xmlStartTag('roleFilter', ['source' => 'title']);
                break;
        }

        foreach ($this->getFilterPattern() as $pattern) {
            $pattern->toXml($writer);
        }
        $writer->xmlEndTag('roleFilter');

        $il_role_id = 'il_' . IL_INST_ID . '_' . ilObject::_lookupType($this->getRoleTemplateId()) . '_' . $this->getRoleTemplateId();

        switch ($this->getRoleTemplateType()) {
            case self::TPL_ACTION_OVERWRITE:
                $writer->xmlStartTag(
                    'localPolicyTemplate',
                    array(
                        'type' => 'overwrite',
                        'id' => $il_role_id
                    )
                );
                break;

            case self::TPL_ACTION_INTERSECT:
                $writer->xmlStartTag(
                    'localPolicyTemplate',
                    array(
                        'type' => 'intersect',
                        'id' => $il_role_id
                    )
                );
                break;

            case self::TPL_ACTION_UNION:
                $writer->xmlStartTag(
                    'localPolicyTemplate',
                    array(
                        'type' => 'union',
                        'id' => $il_role_id
                    )
                );
                break;
        }

        $exp = new ilRoleXmlExport();
        $exp->setMode(ilRoleXmlExport::MODE_DTPL);
        $exp->addRole($this->getRoleTemplateId(), ROLE_FOLDER_ID);
        $exp->write();
        $writer->appendXML($exp->xmlDumpMem(false));
        $writer->xmlEndTag('localPolicyTemplate');
        $writer->xmlEndTag('localPolicyAction');
    }

    /**
     *  clone method
     */
    public function __clone()
    {
        parent::__clone();

        // Clone patterns
        $clones = array();
        foreach ($this->getFilterPattern() as $pattern) {
            $clones[] = clone $pattern;
        }
        $this->setFilterPatterns($clones);
    }

    public function read() : void
    {
        parent::read();
        $query = 'SELECT * FROM didactic_tpl_alp ' .
            'WHERE action_id = ' . $this->db->quote($this->getActionId());
        $res = $this->db->query($query);
        while ($row = $res->fetchRow(ilDBConstants::FETCHMODE_OBJECT)) {
            $this->setFilterType($row->filter_type);
            $this->setRoleTemplateType($row->template_type);
            $this->setRoleTemplateId($row->template_id);
        }
        // Read filter
        foreach (ilDidacticTemplateFilterPatternFactory::lookupPatternsByParentId(
            $this->getActionId(),
            self::PATTERN_PARENT_TYPE) as $pattern) {
            $this->addFilterPattern($pattern);
        }
    }

    protected function createLocalPolicy(ilObject $source, array $role) : bool
    {
        // fetch role information
        $role_data = array();
        foreach ($this->review->getParentRoleIds($source->getRefId()) as $role_id => $tmp_role) {
            if ($role_id == $role['obj_id']) {
                $role_data = $tmp_role;
            }
        }

        // Add local policy
        if (!$this->review->isRoleAssignedToObject($role['obj_id'], $source->getRefId())) {
            $this->admin->assignRoleToFolder(
                $role['obj_id'],
                $source->getRefId(),
                'n'
            );
        }

        // do nothing if role is protected in higher context
        if (
            $this->review->isProtected($source->getRefId(), $role['obj_id']) &&
            $role['parent'] != $source->getRefId()
        ) {
            $this->getLogger()->info('Ignoring protected role: ' . $role['title']);
            return false;
        }

        switch ($this->getRoleTemplateType()) {
            case self::TPL_ACTION_UNION:

                $this->logger->info('Using ilRbacAdmin::copyRolePermissionUnion()');
                $this->admin->copyRolePermissionUnion(
                    $role_data['obj_id'],
                    $role_data['parent'],
                    $this->getRoleTemplateId(),
                    ROLE_FOLDER_ID,
                    $role_data['obj_id'],
                    $source->getRefId()
                );
                break;

            case self::TPL_ACTION_OVERWRITE:

                $this->logger->info('Using ilRbacAdmin::copyRoleTemplatePermission()');
                $this->admin->copyRoleTemplatePermissions(
                    $this->getRoleTemplateId(),
                    ROLE_FOLDER_ID,
                    $source->getRefId(),
                    $role_data['obj_id'],
                    true
                );
                break;

            case self::TPL_ACTION_INTERSECT:

                $this->logger->info('Using ilRbacAdmin::copyRolePermissionIntersection()' . $this->getRoleTemplateId());
                $this->admin->copyRolePermissionIntersection(
                    $role_data['obj_id'],
                    $role_data['parent'],
                    $this->getRoleTemplateId(),
                    ROLE_FOLDER_ID,
                    $source->getRefId(),
                    $role_data['obj_id']
                );
                break;

        }
        // Change existing object
        $role_obj = new ilObjRole($role_data['obj_id']);
        $role_obj->changeExistingObjects(
            $source->getRefId(),
            $role_data['protected'] ? ilObjRole::MODE_PROTECTED_DELETE_LOCAL_POLICIES : ilObjRole::MODE_UNPROTECTED_DELETE_LOCAL_POLICIES,
            array('all')
        );
        return true;
    }

    protected function revertLocalPolicy(ilObject $source, $role) : bool
    {
        $this->logger->info('Reverting policy for role ' . $role['title']);
        // Local policies can only be reverted for auto generated roles. Otherwise the
        // original role settings are unknown
        if (substr($role['title'], 0, 3) != 'il_') {
            $this->logger->warning('Cannot revert local policy for role ' . $role['title']);
            return false;
        }

        // No local policies
        if (!$this->review->getLocalPolicies($source->getRefId())) {
            return false;
        }

        $exploded_title = explode('_', $role['title']);
        $rolt_title = $exploded_title[0] . '_' . $exploded_title[1] . '_' . $exploded_title[2];

        // Lookup role template
        $query = 'SELECT obj_id FROM object_data ' .
            'WHERE title = ' . $this->db->quote($rolt_title, 'text') . ' ' .
            'AND type = ' . $this->db->quote('rolt', 'text');
        $res = $this->db->query($query);
        $rolt_id = 0;
        while ($row = $res->fetchRow(ilDBConstants::FETCHMODE_OBJECT)) {
            $rolt_id = $row->obj_id;
        }

        // No template found
        if (!$rolt_id) {
            return false;
        }

        $this->admin->copyRoleTemplatePermissions(
            $rolt_id,
            ROLE_FOLDER_ID,
            $source->getRefId(),
            $role['obj_id'],
            true
        );

        // Change existing object
        $role_obj = new ilObjRole($role['obj_id']);
        $role_obj->changeExistingObjects(
            $source->getRefId(),
            $role['protected'] ? ilObjRole::MODE_PROTECTED_DELETE_LOCAL_POLICIES : ilObjRole::MODE_UNPROTECTED_DELETE_LOCAL_POLICIES,
            ['all']
        );
        return true;
    }
}
