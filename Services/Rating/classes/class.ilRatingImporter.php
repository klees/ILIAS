<?php

/* Copyright (c) 1998-2021 ILIAS open source, GPLv3, see LICENSE */

/**
 * Importer class for rating (categories)
 *
 * @author Jörg Lützenkirchen <luetzenkirchen@leifos.com>
 */
class ilRatingImporter extends ilXmlImporter
{
    protected ilRatingDataSet $ds;

    /**
     * Initialisation
     */
    public function init() : void
    {
        $this->ds = new ilRatingDataSet();
        $this->ds->setDSPrefix("ds");
    }


    /**
     * @inheritDoc
     */
    public function importXmlRepresentation(string $a_entity, string $a_id, string $a_xml, ilImportMapping $a_mapping) : void
    {
        $parser = new ilDataSetImportParser(
            $a_entity,
            $this->getSchemaVersion(),
            $a_xml,
            $this->ds,
            $a_mapping
        );
    }
}
