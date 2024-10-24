<?php declare(strict_types=1);
/*
    +-----------------------------------------------------------------------------+
    | ILIAS open source                                                           |
    +-----------------------------------------------------------------------------+
    | Copyright (c) 1998-2001 ILIAS open source, University of Cologne            |
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
 * Meta Data class (element annotation)
 * @package ilias-core
 * @version $Id$
 */
class ilMDAnnotation extends ilMDBase
{

    private string $entity = '';
    private string $date = '';
    private string $description = '';
    private ?ilMDLanguageItem $description_language = null;

    // SET/GET
    public function setEntity(string $a_entity) : void
    {
        $this->entity = $a_entity;
    }

    public function getEntity() : string
    {
        return $this->entity;
    }

    public function setDate(string $a_date) : void
    {
        $this->date = $a_date;
    }

    public function getDate() : string
    {
        return $this->date;
    }

    public function setDescription(string $a_desc) : void
    {
        $this->description = $a_desc;
    }

    public function getDescription() : string
    {
        return $this->description;
    }

    public function setDescriptionLanguage(ilMDLanguageItem $lng_obj) : void
    {
        if (is_object($lng_obj)) {
            $this->description_language = $lng_obj;
        }
    }

    public function getDescriptionLanguage() : ilMDLanguageItem
    {
        return $this->description_language;
    }

    public function getDescriptionLanguageCode() : string
    {
        if (is_object($this->description_language)) {
            return $this->description_language->getLanguageCode();
        }
        return '';
    }

    public function save() : int
    {

        $fields                       = $this->__getFields();
        $fields['meta_annotation_id'] = array('integer', $next_id = $this->db->nextId('il_meta_annotation'));

        if ($this->db->insert('il_meta_annotation', $fields)) {
            $this->setMetaId($next_id);
            return $this->getMetaId();
        }
        return 0;
    }

    public function update() : bool
    {

        if ($this->getMetaId()) {
            if ($this->db->update(
                'il_meta_annotation',
                $this->__getFields(),
                array("meta_annotation_id" => array('integer', $this->getMetaId()))
            )) {
                return true;
            }
        }
        return false;
    }

    public function delete() : bool
    {

        if ($this->getMetaId()) {
            $query = "DELETE FROM il_meta_annotation " .
                "WHERE meta_annotation_id = " . $this->db->quote($this->getMetaId(), 'integer');
            $res   = $this->db->manipulate($query);

            return true;
        }
        return false;
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public function __getFields() : array
    {
        return array(
            'rbac_id'              => array('integer', $this->getRBACId()),
            'obj_id'               => array('integer', $this->getObjId()),
            'obj_type'             => array('text', $this->getObjType()),
            'entity'               => array('clob', $this->getEntity()),
            'a_date'               => array('clob', $this->getDate()),
            'description'          => array('clob', $this->getDescription()),
            'description_language' => array('text', $this->getDescriptionLanguageCode())
        );
    }

    public function read() : bool
    {


        if ($this->getMetaId()) {
            $query = "SELECT * FROM il_meta_annotation " .
                "WHERE meta_annotation_id = " . $this->db->quote($this->getMetaId(), 'integer');

            $res = $this->db->query($query);
            while ($row = $res->fetchRow(ilDBConstants::FETCHMODE_OBJECT)) {
                $this->setRBACId((int) $row->rbac_id);
                $this->setObjId((int) $row->obj_id);
                $this->setObjType($row->obj_type);
                $this->setEntity($row->entity);
                $this->setDate($row->a_date);
                $this->setDescription($row->description);
                $this->description_language = new ilMDLanguageItem($row->description_language);
            }
        }
        return true;
    }

    public function toXML(ilXmlWriter $writer) : void
    {
        $writer->xmlStartTag('Annotation');
        $writer->xmlElement('Entity', null, $this->getEntity());
        $writer->xmlElement('Date', null, $this->getDate());
        $writer->xmlElement(
            'Description',
            array(
                'Language' => $this->getDescriptionLanguageCode()
                    ? $this->getDescriptionLanguageCode()
                    : 'en'
            ),
            $this->getDescription()
        );
        $writer->xmlEndTag('Annotation');
    }



    // STATIC

    /**
     * @return int[]
     */
    public static function _getIds(int $a_rbac_id, int $a_obj_id) : array
    {
        global $DIC;

        $ilDB = $DIC['ilDB'];

        $query = "SELECT meta_annotation_id FROM il_meta_annotation " .
            "WHERE rbac_id = " . $ilDB->quote($a_rbac_id, 'integer') . " " .
            "AND obj_id = " . $ilDB->quote($a_obj_id, 'integer');

        $res = $ilDB->query($query);
        $ids = [];
        while ($row = $res->fetchRow(ilDBConstants::FETCHMODE_OBJECT)) {
            $ids[] = (int) $row->meta_annotation_id;
        }
        return $ids;
    }
}
