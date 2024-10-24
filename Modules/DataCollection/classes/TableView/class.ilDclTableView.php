<?php

/**
 * Class ilDclTableView
 * @author  Theodor Truffer <tt@studer-raimann.ch>
 * @ingroup ModulesDataCollection
 */
class ilDclTableView extends ActiveRecord
{

    /**
     * @var int
     * @db_has_field        true
     * @db_is_unique        true
     * @db_is_primary       true
     * @db_is_notnull       true
     * @db_fieldtype        integer
     * @db_length           8
     * @db_sequence         true
     */
    protected $id;
    /**
     * @var int
     * @db_has_field        true
     * @db_is_notnull       true
     * @db_fieldtype        integer
     * @db_length           8
     */
    protected $table_id;
    /**
     * @var string
     * @db_has_field        true
     * @db_is_notnull       true
     * @db_fieldtype        text
     * @db_length           128
     */
    protected $title;
    /**
     * @var array
     * @db_has_field        true
     * @db_fieldtype        text
     * @db_length           256
     */
    protected $roles = array();
    /**
     * @var string
     * @db_has_field        true
     * @db_fieldtype        text
     * @db_length           128
     */
    protected $description;
    /**
     * @var int
     * @db_has_field        true
     * @db_fieldtype        integer
     * @db_length           8
     */
    protected $tableview_order;
    /**
     * @var bool
     * @db_has_field        true
     * @db_fieldtype        integer
     * @db_length           1
     */
    protected $step_vs;
    /**
     * @var bool
     * @db_has_field        true
     * @db_fieldtype        integer
     * @db_length           1
     */
    protected $step_c;
    /**
     * @var bool
     * @db_has_field        true
     * @db_fieldtype        integer
     * @db_length           1
     */
    protected $step_e;
    /**
     * @var bool
     * @db_has_field        true
     * @db_fieldtype        integer
     * @db_length           1
     */
    protected $step_o;
    /**
     * @var bool
     * @db_has_field        true
     * @db_fieldtype        integer
     * @db_length           1
     */
    protected $step_s;
    /**
     * @var ilDclBaseFieldModel[]
     */
    protected $visible_fields_cache;

    /**
     * @return string
     * @description Return the Name of your Database Table
     */
    public static function returnDbTableName() : string
    {
        return "il_dcl_tableview";
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getTableId()
    {
        return $this->table_id;
    }

    /**
     * @param int $table_id
     */
    public function setTableId($table_id)
    {
        $this->table_id = $table_id;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return int
     */
    public function getOrder()
    {
        return $this->tableview_order;
    }

    /**
     * @param int $order
     */
    public function setOrder($order)
    {
        $this->tableview_order = $order;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return int
     */
    public function getTableviewOrder()
    {
        return $this->tableview_order;
    }

    /**
     * @param int $tableview_order
     */
    public function setTableviewOrder($tableview_order)
    {
        $this->tableview_order = $tableview_order;
    }

    /**
     * @return bool
     */
    public function isStepVs()
    {
        return $this->step_vs;
    }

    /**
     * @param bool $step_vs
     */
    public function setStepVs($step_vs)
    {
        $this->step_vs = $step_vs;
    }

    /**
     * @return bool
     */
    public function isStepC()
    {
        return $this->step_c;
    }

    /**
     * @param bool $step_c
     */
    public function setStepC($step_c)
    {
        $this->step_c = $step_c;
    }

    /**
     * @return bool
     */
    public function isStepE()
    {
        return $this->step_e;
    }

    /**
     * @param bool $step_e
     */
    public function setStepE($step_e)
    {
        $this->step_e = $step_e;
    }

    /**
     * @return bool
     */
    public function isStepO()
    {
        return $this->step_o;
    }

    /**
     * @param bool $step_o
     */
    public function setStepO($step_o)
    {
        $this->step_o = $step_o;
    }

    /**
     * @return bool
     */
    public function isStepS()
    {
        return $this->step_s;
    }

    /**
     * @param bool $step_s
     */
    public function setStepS($step_s)
    {
        $this->step_s = $step_s;
    }

    /**
     * @return array
     */
    public function getRoles()
    {
        return (array) $this->roles;
    }

    /**
     * @param array $roles
     */
    public function setRoles(array $roles)
    {
        $this->roles = $roles;
    }

    /**
     * @param $field_name
     * @return null|string
     */
    public function sleep($field_name)
    {
        if ($field_name == 'roles') {
            return json_encode($this->roles);
        }

        return null;
    }

    /**
     * @param $field_name
     * @param $field_value
     * @return mixed|null
     */
    public function wakeUp($field_name, $field_value)
    {
        if ($field_name == 'roles') {
            return json_decode($field_value);
        }

        return null;
    }

    /**
     *
     */
    public function delete()
    {
        //Delete settings
        foreach ($this->getFieldSettings() as $setting) {
            $setting->delete();
        }
        parent::delete();
    }

    /**
     * @return ilDclTable
     */
    public function getTable()
    {
        return ilDclCache::getTableCache($this->table_id);
    }

    /**
     * getFilterableFields
     * Returns all  fieldsetting-objects of this tableview which have set their filterable to true, including standard fields.
     * @return ilDclBaseFieldModel[]|ilDclTableViewFieldSetting[]
     */
    public function getFilterableFieldSettings()
    {
        return ilDclTableViewFieldSetting::where(
            array(
                "tableview_id" => $this->id,
                'in_filter' => 1,
                'il_dcl_tfield_set.table_id' => $this->getTableId(),
            )
        )->innerjoin('il_dcl_tfield_set', 'field', 'field', array())
                                         ->orderBy('il_dcl_tfield_set.field_order')
                                         ->get();
    }

    /**
     * Returns all field-objects of this tableview which have set their visibility to true, including standard fields.
     * @return ilDclBaseFieldModel[]|ilDclTableViewFieldSetting[]
     */
    public function getVisibleFields()
    {
        if (!$this->visible_fields_cache) {
            $visible = ilDclTableViewFieldSetting::
            where(
                array(
                    "tableview_id" => $this->id,
                    'visible' => true,
                    'il_dcl_tfield_set.table_id' => $this->getTableId(),
                )
            )->innerjoin('il_dcl_tfield_set', 'field', 'field',
                array())->orderBy('il_dcl_tfield_set.field_order')->get();
            $fields = array();
            foreach ($visible as $field_rec) {
                $fields[] = $field_rec->getFieldObject();
            }
            $this->visible_fields_cache = $fields;
        }

        return $this->visible_fields_cache;
    }

    /**
     * @return ilDclTableViewFieldSetting[]
     * @throws arException
     */
    public function getFieldSettings() : array
    {
        return ilDclTableViewFieldSetting::where(
            array(
                'tableview_id' => $this->getId(),
                'il_dcl_tfield_set.table_id' => $this->getTableId(),
            )
        )->innerjoin('il_dcl_tfield_set', 'field', 'field', array('field_order'))->orderBy('field_order')->get();
    }

    /**
     * @param $field_id
     * @return ilDclTableViewFieldSetting
     */
    public function getFieldSetting($field_id) : ilDclTableViewFieldSetting
    {
        return ilDclTableViewFieldSetting::where([
            'tableview_id' => $this->getId(),
            'field' => $field_id
        ])->first();
    }

    /**
     * @param bool $create_default_settings
     */
    public function create($create_default_settings = true) : void
    {
        parent::create();
        if ($create_default_settings) {
            $this->createDefaultSettings();
        }
    }

    /**
     * create default ilDclTableViewFieldSetting entries
     */
    public function createDefaultSettings()
    {
        $table = ilDclCache::getTableCache($this->table_id);

        foreach ($table->getFieldIds() as $field_id) {
            $this->createFieldSetting($field_id);
        }

        //ilDclTable->getFieldIds won't reuturn comments if they are disabled,
        //still we have to create a fieldsetting for this field
        if (!$table->getPublicCommentsEnabled()) {
            $this->createFieldSetting('comments');
        }
    }

    /**
     * create ilDclTableViewFieldSetting for this tableview and the given field id
     * @param $field_id
     */
    public function createFieldSetting($field_id)
    {
        if (!ilDclTableViewFieldSetting::where(
            array(
                'tableview_id' => $this->id,
                'field' => $field_id,
            )
        )->get()
        ) {
            $field_set = new ilDclTableViewFieldSetting();
            $field_set->setTableviewId($this->id);
            $field_set->setField($field_id);
            $field_set->setVisible(!ilDclStandardField::_isStandardField($field_id));
            $field_set->setFilterChangeable(true);
            $field_set->setLockedCreate(false);
            $field_set->setLockedEdit(false);
            $field_set->setRequiredCreate(false);
            $field_set->setRequiredEdit(false);
            $field_set->setVisibleCreate(true);
            $field_set->setVisibleEdit(true);
            $field_set->create();
        }
    }

    /**
     * @param ilDclTableView $orig
     * @param array          $new_fields fields mapping
     */
    public function cloneStructure(ilDclTableView $orig, array $new_fields)
    {
        //clone structure
        $this->setTitle($orig->getTitle());
        $this->setOrder($orig->getOrder());
        $this->setDescription($orig->getDescription());
        $this->setRoles($orig->getRoles());
        $this->setStepVs($orig->isStepVs());
        $this->setStepC($orig->isStepC());
        $this->setStepE($orig->isStepE());
        $this->setStepO($orig->isStepO());
        $this->setStepS($orig->isStepS());
        $this->create(false); //create default setting, adjust them later

        //clone default values
        $f = new ilDclDefaultValueFactory();

        //clone fieldsettings
        foreach ($orig->getFieldSettings() as $orig_fieldsetting) {
            $new_fieldsetting = new ilDclTableViewFieldSetting();
            $new_fieldsetting->setTableviewId($this->getId());
            if ($new_fields[$orig_fieldsetting->getField()] ?? null) {
                //normal fields
                $new_fieldsetting->setField($new_fields[$orig_fieldsetting->getField()]->getId());
            } else {
                //standard fields
                $new_fieldsetting->setField($orig_fieldsetting->getField());
            }
            $new_field_id = $new_fieldsetting->cloneStructure($orig_fieldsetting);

            //clone default value
            $datatype = $orig_fieldsetting->getFieldObject()->getDatatypeId();
            $match = ilDclTableViewBaseDefaultValue::findSingle($datatype, $orig_fieldsetting->getId());

            if (!is_null($match)) {
                $new_default_value = $f->create($datatype);
                $new_default_value->setTviewSetId($new_field_id);
                $new_default_value->setValue($match->getValue());
                $new_default_value->create();
            }
        }

        //clone pageobject
        if (ilDclDetailedViewDefinition::exists($orig->getId())) {
            $orig_pageobject = new ilDclDetailedViewDefinition($orig->getId());
            $orig_pageobject->copy($this->getId());
        }

        // mandatory for all cloning functions
        ilDclCache::setCloneOf($orig->getId(), $this->getId(), ilDclCache::TYPE_TABLEVIEW);
    }

    /**
     * @param $table_id
     * @return ilDclTableView[]
     */
    public static function getAllForTableId($table_id)
    {
        return self::where(array('table_id' => $table_id))->orderBy('tableview_order')->get();
    }

    /**
     * @param $table_id
     * @return int
     */
    public static function getCountForTableId($table_id)
    {
        return self::where(array('table_id' => $table_id))->orderBy('tableview_order')->count();
    }

    /**
     * @param      $table_id
     * @param bool $create_default_settings
     * @return ilDclTableView
     */
    public static function createOrGetStandardView($table_id, $create_default_settings = true)
    {
        if ($standardview = self::where(array('table_id' => $table_id))->orderBy('tableview_order')->first()) {
            return $standardview;
        }

        global $DIC;
        $rbacreview = $DIC['rbacreview'];
        $roles = array();
        foreach ($rbacreview->getParentRoleIds($_GET['ref_id']) as $role_array) {
            $roles[] = $role_array['obj_id'];
        }

        $view = new self();

        if ($_GET['ref_id']) {
            global $DIC;
            $rbacreview = $DIC['rbacreview'];
            $roles = array();
            foreach ($rbacreview->getParentRoleIds($_GET['ref_id']) as $role_array) {
                $roles[] = $role_array['obj_id'];
            }
            $view->setRoles(array_merge($roles, $rbacreview->getLocalRoles($_GET['ref_id'])));
        }
        $view->setTableId($table_id);
        // bugfix mantis 0023307
        $lng = $DIC['lng'];
        $view->setTitle($lng->txt('dcl_title_standardview'));
        $view->setTableviewOrder(10);
        $view->setStepVs(true);
        $view->setStepC(false);
        $view->setStepE(false);
        $view->setStepO(false);
        $view->setStepS(false);
        $view->create($create_default_settings);

        return $view;
    }

    /**
     * Check if the configuration of the view is complete. The step "single" is
     * optional and therefore omitted.
     * @return bool
     */
    public function validateConfigCompletion()
    {
        return $this->step_vs && $this->step_c && $this->step_e && $this->step_o;
    }
}
