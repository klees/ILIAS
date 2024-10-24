<?php declare(strict_types=1);
/* Copyright (c) 1998-2009 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * Settings for a single didactic template
 * @author   Stefan Meyer <meyer@leifos.com>
 * @defgroup ServicesDidacticTemplate
 */
class ilDidacticTemplateSetting
{
    public const TYPE_CREATION = 1;

    private int $id = 0;
    private bool $enabled = false;
    private string $title = '';
    private string $description = '';
    private string $info = '';
    private int $type = self::TYPE_CREATION;
    private array $assignments = array();
    private array $effective_from = array();
    private bool $auto_generated = false;
    private bool $exclusive = false;
    private string $icon_ide = '';

    private ?ilDidacticTemplateIconHandler $iconHandler = null;

    private ilLanguage $lng;
    private ilObjUser $user;
    private ilDBInterface $db;
    private ilSetting $setting;
    private ilTree $tree;

    /**
     * Constructor
     * @param int $a_id
     */
    public function __construct(int $a_id = 0)
    {
        global $DIC;

        $this->lng = $DIC->language();
        $this->user = $DIC->user();
        $this->db = $DIC->database();
        $this->setting = $DIC->settings();
        $this->tree = $DIC->repositoryTree();

        $this->setId($a_id);
        $this->read();
        $this->iconHandler = new ilDidacticTemplateIconHandler($this);
    }

    /**
     * @return ilDidacticTemplateIconHandler
     */
    public function getIconHandler() : ilDidacticTemplateIconHandler
    {
        return $this->iconHandler;
    }

    /**
     * Set id
     * @param int $a_id
     */
    protected function setId(int $a_id) : void
    {
        $this->id = $a_id;
    }

    /**
     * Get id
     * @return int
     */
    public function getId() : int
    {
        return $this->id;
    }

    /**
     * Set enabled status
     * @param bool $a_status
     */
    public function enable(bool $a_status) : void
    {
        $this->enabled = $a_status;
    }

    /**
     * Check if template is enabled
     * @return bool
     */
    public function isEnabled() : bool
    {
        return $this->enabled;
    }

    /**
     * Set title
     * @param string $a_title
     */
    public function setTitle(string $a_title) : void
    {
        $this->title = $a_title;
    }

    /**
     * Get title
     * @return string
     */
    public function getTitle() : string
    {
        return $this->title;
    }

    /**
     * @param string $a_lng
     * @return string
     */
    public function getPresentationTitle(string $a_lng = "") : string
    {
        if ($this->isAutoGenerated()) {
            return $this->lng->txt($this->getTitle());
        }

        $tit = $this->getPresentation('title', $a_lng);
        return $tit ? $tit : $this->getTitle();
    }

    /**
     * Get description
     * @return string
     */
    public function getDescription() : string
    {
        return $this->description;
    }

    /**
     * @param string $a_lng
     * @return string
     */
    public function getPresentationDescription(string $a_lng = "") : string
    {
        if ($this->isAutoGenerated()) {
            return $this->lng->txt($this->getDescription());
        }

        $desc = $this->getPresentation('description', $a_lng);
        return $desc ? $desc : $this->getDescription();
    }

    /**
     * Set description
     * @param string $a_description
     */
    public function setDescription(string $a_description) : void
    {
        $this->description = $a_description;
    }

    /**
     * Set installation info text
     * @param string $a_info
     */
    public function setInfo(string $a_info) : void
    {
        $this->info = $a_info;
    }

    /**
     * Get installation info text
     * @return string
     */
    public function getInfo() : string
    {
        return $this->info;
    }

    /**
     * Set type
     * @param int $a_type
     */
    public function setType(int $a_type) : void
    {
        $this->type = $a_type;
    }

    /**
     * Get type
     * @return int
     */
    public function getType() : int
    {
        return $this->type;
    }

    /**
     * @param ilObjectDefinition $definition
     * @return bool
     */
    public function hasIconSupport(ilObjectDefinition $definition) : bool
    {
        foreach ($this->getAssignments() as $assignment) {
            if (!$definition->isContainer($assignment)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Set assignments
     * @param array $a_ass
     */
    public function setAssignments(array $a_ass) : void
    {
        $this->assignments = (array) $a_ass;
    }

    /**
     * Get object assignemnts
     * @return array
     */
    public function getAssignments() : array
    {
        return (array) $this->assignments;
    }

    /**
     * Add one assignment obj type
     * @param string $a_obj_type
     */
    public function addAssignment(string $a_obj_type) : void
    {
        $this->assignments[] = $a_obj_type;
    }

    /**
     * @return int[]
     */
    public function getEffectiveFrom() : array
    {
        return $this->effective_from;
    }

    /**
     * @param int[] $effective_from
     */
    public function setEffectiveFrom(array $effective_from) : void
    {
        $this->effective_from = $effective_from;
    }

    /**
     * @return bool
     */
    public function isAutoGenerated() : bool
    {
        return $this->auto_generated;
    }

    /**
     * DO NOT CHANGE THIS VALUE
     * @param bool $auto_generated
     */
    private function setAutoGenerated(bool $auto_generated) : void
    {
        $this->auto_generated = $auto_generated;
    }

    public function isExclusive() : bool
    {
        return $this->exclusive;
    }

    /**
     * @param bool $exclusive
     */
    public function setExclusive(bool $exclusive) : void
    {
        $this->exclusive = $exclusive;
    }

    public function setIconIdentifier(string $icon_identifier) : void
    {
        $this->icon_ide = $icon_identifier;
    }

    public function getIconIdentifier() : string
    {
        return $this->icon_ide;
    }

    /**
     * get all translations from this object
     * @access    public
     * @return    array
     */
    public function getTranslations() : array
    {
        $trans = $this->getTranslationObject();
        $lang = $trans->getLanguages();

        foreach ($lang as $k => $v) {
            if ($v['lang_default']) {
                $lang[0] = $lang[$k];
            }
        }

        // fallback if translation object is empty
        if (!isset($lang[0])) {
            $lang[0]['title'] = $this->getTitle();
            $lang[0]['description'] = $this->getDescription();
            $lang[0]['lang_code'] = $trans->getDefaultLanguage();
        }

        return $lang;
    }

    protected function getPresentation(string $a_value, string $a_lng) : string
    {
        $lang = $this->getTranslationObject()->getLanguages();

        if (!$lang) {
            return "";
        }

        if (!$a_lng) {
            $a_lng = $this->user->getCurrentLanguage();
        }

        if (isset($lang[$a_lng][$a_value])) {
            return $lang[$a_lng][$a_value];
        } else {
            return $lang[$a_lng][$this->getTranslationObject()->getDefaultLanguage()];
        }
    }

    /**
     * Delete settings
     */
    public function delete() : bool
    {
        if ($this->isAutoGenerated()) {
            return false;
        }

        // Delete settings
        $query = 'DELETE FROM didactic_tpl_settings ' .
            'WHERE id = ' . $this->db->quote($this->getId(), 'integer');
        $this->db->manipulate($query);

        // Delete obj assignments
        $query = 'DELETE FROM didactic_tpl_sa ' .
            'WHERE id = ' . $this->db->quote($this->getId(), 'integer');
        $this->db->manipulate($query);

        foreach (ilDidacticTemplateActionFactory::getActionsByTemplateId($this->getId()) as $action) {
            $action->delete();
        }
        ilDidacticTemplateObjSettings::deleteByTemplateId($this->getId());
        $this->getTranslationObject()->delete();
        $this->deleteEffectiveNodes();
        $this->getIconHandler()->delete();
        return true;
    }

    /**
     * Save settings
     */
    public function save() : bool
    {
        $this->setId((int) $this->db->nextId('didactic_tpl_settings'));
        $query = 'INSERT INTO didactic_tpl_settings (id,enabled,title,description,info,type,auto_generated,exclusive_tpl,icon_ide) ' .
            'VALUES( ' .
            $this->db->quote($this->getId(), 'integer') . ', ' .
            $this->db->quote($this->isEnabled(), 'integer') . ', ' .
            $this->db->quote($this->getTitle(), 'text') . ', ' .
            $this->db->quote($this->getDescription(), 'text') . ', ' .
            $this->db->quote($this->getInfo(), 'text') . ', ' .
            $this->db->quote($this->getType(), 'integer') . ', ' .
            $this->db->quote((int) $this->isAutoGenerated(), 'integer') . ', ' .
            $this->db->quote((int) $this->isExclusive(), 'integer') . ', ' .
            $this->db->quote((string) $this->getIconIdentifier(), ilDBConstants::T_TEXT) . ' ' .
            ')';

        $this->db->manipulate($query);
        $this->saveAssignments();
        return true;
    }

    /**
     * Save assignments in DB
     * @return bool
     */
    private function saveAssignments() : bool
    {
        if ($this->isAutoGenerated()) {
            return false;
        }

        foreach ($this->getAssignments() as $ass) {
            $this->saveAssignment($ass);
        }
        return true;
    }

    /**
     * Add one object assignment
     * @param string $a_obj_type
     */
    private function saveAssignment(string $a_obj_type) : void
    {
        if ($this->isAutoGenerated()) {
            return;
        }

        $query = 'INSERT INTO didactic_tpl_sa (id,obj_type) ' .
            'VALUES( ' .
            $this->db->quote($this->getId(), 'integer') . ', ' .
            $this->db->quote($a_obj_type, 'text') .
            ')';
        $this->db->manipulate($query);
    }

    /**
     *
     */
    protected function saveEffectiveNodes() : void
    {
        if (!count($this->getEffectiveFrom())) {
            return;
        }
        $values = [];
        foreach ($this->getEffectiveFrom() as $node) {
            $values[] = '( ' .
                $this->db->quote($this->getId(), 'integer') . ', ' .
                $this->db->quote($node, 'integer') .
                ')';
        }

        $query = 'INSERT INTO didactic_tpl_en (id,node) ' .
            'VALUES ' . implode(', ', $values);

        $this->db->manipulate($query);
    }

    protected function deleteEffectiveNodes() : bool
    {
        $query = 'DELETE FROM didactic_tpl_en ' .
            'WHERE id = ' . $this->db->quote($this->getId(), 'integer');
        $this->db->manipulate($query);
        return true;
    }

    protected function readEffectiveNodes() : void
    {
        $effective_nodes = array();

        $query = 'SELECT * FROM didactic_tpl_en ' .
            'WHERE id = ' . $this->db->quote($this->getId(), 'integer');
        $res = $this->db->query($query);
        while ($row = $res->fetchRow(ilDBConstants::FETCHMODE_OBJECT)) {
            $effective_nodes[] = $row->node;
        }

        $this->setEffectiveFrom($effective_nodes);
    }

    /**
     * Delete assignments
     * @return bool
     */
    private function deleteAssignments() : bool
    {
        if ($this->isAutoGenerated()) {
            return false;
        }

        $query = 'DELETE FROM didactic_tpl_sa ' .
            'WHERE id = ' . $this->db->quote($this->getId(), 'integer');
        $this->db->manipulate($query);
        return true;
    }

    /**
     * Update settings
     * @return bool
     */
    public function update() : bool
    {
        $query = 'UPDATE didactic_tpl_settings ' .
            'SET ' .
            'enabled = ' . $this->db->quote($this->isEnabled(), 'integer') . ', ' .
            'title = ' . $this->db->quote($this->getTitle(), 'text') . ', ' .
            'description = ' . $this->db->quote($this->getDescription(), 'text') . ', ' .
            'info = ' . $this->db->quote($this->getInfo(), 'text') . ', ' .
            'type = ' . $this->db->quote($this->getType(), 'integer') . ', ' .
            'exclusive_tpl = ' . $this->db->quote((int) $this->isExclusive(), 'integer') . ', ' .
            'icon_ide = ' . $this->db->quote((string) $this->getIconIdentifier(), ilDBConstants::T_TEXT) . ' ' .
            'WHERE id = ' . $this->db->quote($this->getId(), 'integer');
        $this->db->manipulate($query);
        $this->deleteAssignments();
        $this->saveAssignments();
        $this->deleteEffectiveNodes();
        $this->saveEffectiveNodes();
        return true;
    }

    /**
     * read settings from db
     * @return bool
     */
    protected function read() : bool
    {
        if (!$this->getId()) {
            return false;
        }
        $query = 'SELECT * FROM didactic_tpl_settings dtpl ' .
            'WHERE id = ' . $this->db->quote($this->getId(), 'integer');
        $res = $this->db->query($query);
        while ($row = $res->fetchRow(ilDBConstants::FETCHMODE_OBJECT)) {
            $this->setType((int) $row->type);
            $this->enable((bool) $row->enabled);
            $this->setTitle((string) $row->title);
            $this->setDescription((string) $row->description);
            $this->setInfo((string) $row->info);
            $this->setAutoGenerated((bool) $row->auto_generated);
            $this->setExclusive((bool) $row->exclusive_tpl);
            $this->setIconIdentifier((string) $row->icon_ide);
        }
        $query = 'SELECT * FROM didactic_tpl_sa ' .
            'WHERE id = ' . $this->db->quote($this->getId(), 'integer');
        $res = $this->db->query($query);
        while ($row = $res->fetchRow(ilDBConstants::FETCHMODE_OBJECT)) {
            $this->addAssignment($row->obj_type);
        }
        $this->readEffectiveNodes();
        return true;
    }

    /**
     * Export
     * @param ilXmlWriter $writer
     * @return ilXmlWriter
     */
    public function toXml(ilXmlWriter $writer) : ilXmlWriter
    {
        $type = '';
        switch ($this->getType()) {
            case self::TYPE_CREATION:
                $type = 'creation';
                break;
        }
        $writer->xmlStartTag('didacticTemplate', array('type' => $type));
        $writer->xmlElement('title', array(), $this->getTitle());
        $writer->xmlElement('description', array(), $this->getDescription());
        $writer = $this->getIconHandler()->toXml($writer);
        $writer = $this->getTranslationObject()->toXml($writer);

        // info text with p-tags
        if (strlen($this->getInfo())) {
            $writer->xmlStartTag('info');

            $info_lines = (array) explode("\n", $this->getInfo());
            foreach ($info_lines as $info) {
                $trimmed_info = trim($info);
                if (strlen($trimmed_info)) {
                    $writer->xmlElement('p', array(), $trimmed_info);
                }
            }

            $writer->xmlEndTag('info');
        }
        if ($this->isExclusive()) {
            $writer->xmlElement("exclusive");
        }
        if (count($this->getEffectiveFrom()) > 0) {
            $writer->xmlStartTag('effectiveFrom', array('nic_id' => $this->setting->get('inst_id')));

            foreach ($this->getEffectiveFrom() as $node) {
                $writer->xmlElement('node', array(), $node);
            }
            $writer->xmlEndTag('effectiveFrom');
        }
        // Assignments
        $writer->xmlStartTag('assignments');
        foreach ($this->getAssignments() as $assignment) {
            $writer->xmlElement('assignment', array(), $assignment);
        }
        $writer->xmlEndTag('assignments');
        $writer->xmlStartTag('actions');
        foreach (ilDidacticTemplateActionFactory::getActionsByTemplateId($this->getId()) as $action) {
            $action->toXml($writer);
        }
        $writer->xmlEndTag('actions');
        $writer->xmlEndTag('didacticTemplate');

        return $writer;
    }

    /**
     * Implemented clone method
     */
    public function __clone()
    {
        $this->setId(0);

        $this->setTitle(ilDidacticTemplateCopier::appendCopyInfo($this->getTitle()));
        $this->enable(false);
        $this->setAutoGenerated(false);
        $this->iconHandler = new ilDidacticTemplateIconHandler($this);
    }

    /**
     * @return ilMultilingualism
     */
    public function getTranslationObject() : ilMultilingualism
    {
        return ilMultilingualism::getInstance($this->getId(), "dtpl");
    }

    /**
     * @param int $a_node_id
     * @return bool
     */
    public function isEffective($a_node_id) : bool
    {
        if (!count($this->getEffectiveFrom()) || in_array($a_node_id, $this->getEffectiveFrom())) {
            return true;
        }

        foreach ($this->getEffectiveFrom() as $node) {
            if ($this->tree->isGrandChild($node, $a_node_id)) {
                return true;
            }
        }
        return false;
    }
}
