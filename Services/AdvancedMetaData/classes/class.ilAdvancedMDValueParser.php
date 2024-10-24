<?php declare(strict_types=1);
/*
    +-----------------------------------------------------------------------------+
    | ILIAS open source                                                           |
    +-----------------------------------------------------------------------------+
    | Copyright (c) 1998-2006 ILIAS open source, University of Cologne            |
    |                                                                             |
    | This program is free software; you can redistribute it and/or               |
    | modify it under the terms of the GNU General Public License                 |
    | as published by the Free Software Foundation; either version 2              |
    | of the License, or (at your option) any later version.                      |
    |                                                                             |
    | This program is distributed in the hope that it will be useful,             |
    | but WITHOUT ANY WARRANTY; without even the implied warranty of              |
    | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the               |
    | GNU General Public License for more details.                                |
    |                                                                             |
    | You should have received a copy of the GNU General Public License           |
    | along with this program; if not, write to the Free Software                 |
    | Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA. |
    +-----------------------------------------------------------------------------+
*/

/**
 * @author  Stefan Meyer <meyer@leifos.com>
 * @version $Id$
 * @ingroup ServicesAdvancedMetaData
 */
class ilAdvancedMDValueParser implements ilSaxSubsetParser
{
    protected string $cdata = '';
    protected int $obj_id;
    protected array $values_records = array();
    protected array $values = array();
    protected ?ilAdvancedMDFieldDefinition $current_value = null;

    public function __construct(int $a_new_obj_id = 0)
    {
        $this->obj_id = $a_new_obj_id;
    }

    /**
     * Set object id (id of new created object)
     */
    public function setObjId(int $a_obj_id) : void
    {
        $this->obj_id = $a_obj_id;
    }

    /**
     * Save values
     * @access public
     */
    public function save() : bool
    {
        foreach ($this->values_records as $values_record) {
            $values_record->write();
        }
        return true;
    }

    /**
     * Start element handler
     * @access public
     * @param resource $a_xml_parser xml parser
     * @param string   $a_name       element name
     * @param array    $a_attribs    element attributes array
     */
    public function handlerBeginTag($a_xml_parser, string $a_name, array $a_attribs) : void
    {
        switch ($a_name) {
            case 'AdvancedMetaData':
                $this->values_records = ilAdvancedMDValues::getInstancesForObjectId($this->obj_id);
                foreach ($this->values_records as $values_record) {
                    // init ADTGroup before definitions to bind definitions to group
                    $values_record->getADTGroup();

                    foreach ($values_record->getDefinitions() as $def) {
                        $this->values[$def->getImportId()] = $def;
                    }
                }
                break;

            case 'Value':
                $this->initValue($a_attribs['id']);
                break;
        }
    }

    /**
     * End element handler
     * @access public
     * @param resource $a_xml_parser xml parser
     * @param string   $a_name       element name
     */
    public function handlerEndTag($a_xml_parser, string $a_name) : void
    {
        switch ($a_name) {
            case 'AdvancedMetaData':
                break;

            case 'Value':
                $value = trim($this->cdata);
                if (is_object($this->current_value) && $value) {
                    $this->current_value->importValueFromXML($value);
                }
                break;
        }
        $this->cdata = '';
    }

    /**
     * Character data handler
     * @access public
     * @param resource $a_xml_parser xml parser
     * @param string   $a_data       character data
     */
    public function handlerCharacterData($a_xml_parser, string $a_data) : void
    {
        if ($a_data != "\n") {
            // Replace multiple tabs with one space
            $a_data = preg_replace("/\t+/", " ", $a_data);

            $this->cdata .= $a_data;
        }
    }

    /**
     * init new value object
     */
    private function initValue(string $a_import_id) : void
    {
        if (isset($this->values[$a_import_id])) {
            $this->current_value = $this->values[$a_import_id];
        } else {
            $this->current_value = null;
        }
    }
}
