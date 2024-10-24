<?php
/* Copyright (c) 1998-2010 ILIAS open source, Extended GPL, see docs/LICENSE */


include_once './Modules/WebResource/classes/class.ilWebLinkXmlWriter.php';
include_once './Services/Export/classes/class.ilXmlExporter.php';

/**
* Booking definition
*
* @author Stefan Meyer <meyer@leifos.com>
*
* @version $Id$
*
* @ingroup ServicesBooking
*/
class ilWebResourceExporter extends ilXmlExporter
{
    private $writer = null;

    /**
     * Constructor
     */
    public function __construct()
    {
    }
    
    /**
     * Init export
     * @return void
     */
    public function init() : void
    {
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
        try {
            $this->writer = new ilWebLinkXmlWriter(false);
            $this->writer->setObjId($a_id);
            $this->writer->write();
            return $this->writer->xmlDumpMem(false);
        } catch (UnexpectedValueException $e) {
            $GLOBALS['DIC']->logger()->webr()->warning("Caught error: " . $e->getMessage());
            return '';
        }
    }

    /**
     * Get tail dependencies
     * @param		string		entity
     * @param		string		target release
     * @param		array		ids
     * @return        array        array of array with keys "component", entity", "ids"
     */
    public function getXmlExportTailDependencies(string $a_entity, string $a_target_release, array $a_ids) : array
    {
        $deps = [];

        // service settings
        $deps[] = [
            "component" => "Services/Object",
            "entity" => "common",
            "ids" => $a_ids
        ];

        return $deps;
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
            "4.1.0" => array(
                "namespace" => "http://www.ilias.de/Modules/WebResource/webr/4_1",
                "xsd_file" => "ilias_webr_4_1.xsd",
                "uses_dataset" => false,
                "min" => "4.1.0",
                "max" => "")
        );
    }
}
