<?php declare(strict_types=1);
/* Copyright (c) 1998-2010 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * Role Exporter
 * @author  Stefan Meyer <meyer@leifos.com>
 * @ingroup ServicesAccessControl
 */
class ilAccessControlExporter extends ilXmlExporter
{
    public function init() : void
    {
    }

    /**
     * Get head dependencies
     * @param string        entity
     * @param string        target release
     * @param array        ids
     * @return        array        array of array with keys "component", entity", "ids"
     */
    public function getXmlExportHeadDependencies(string $a_entity, string $a_target_release, array $a_ids) : array
    {
        return [];
    }

    /**
     * Get xml
     * @param string $a_entity
     * @param string $a_schema_version
     * @param string $a_id
     * @return string
     */
    public function getXmlRepresentation(string $a_entity, string $a_schema_version, string $a_id) : string
    {
        global $DIC;

        $rbacreview = $DIC['rbacreview'];

        $writer = new ilRoleXmlExport();

        $eo = ilExportOptions::getInstance();
        $eo->read();
    
        $rolf = $eo->getOptionByObjId((int) $a_id, ilExportOptions::KEY_ROOT);
        $writer->setRoles(array($a_id => $rolf));
        $writer->write();
        return $writer->xmlDumpMem(false);
    }

    /**
     * Returns schema versions that the component can export to.
     * ILIAS chooses the first one, that has min/max constraints which
     * fit to the target release. Please put the newest on top.
     * @return array
     */
    public function getValidSchemaVersions(string $a_entity) : array
    {
        return array(
            "4.3.0" => array(
                "namespace" => "http://www.ilias.de/AccessControl/Role/role/4_3",
                "xsd_file" => "ilias_role_4_3.xsd",
                "uses_dataset" => false,
                "min" => "4.3.0",
                "max" => ""
            )
        );
    }
}
