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
* Parses Lucene search results
*
* @author Stefan Meyer <meyer@leifos.com>
*
*
* @ingroup ServicesSearch
*/
class ilLuceneSearchResultParser
{
    private string $xml;
    
    /**
     * Constructor
     */
    public function __construct(string $a_xml)
    {
        $this->xml = $a_xml;
    }
    

    /**
     * get xml
     * @param
     * @return string
     */
    public function getXML() : string
    {
        return $this->xml;
    }
    
    /**
     * Parse XML
     */
    public function parse(ilLuceneSearchResult $result) : ilLuceneSearchResult
    {
        if (!strlen($this->getXML())) {
            return $result;
        }
        $hits = new SimpleXMLElement($this->getXML());
        $result->setLimit($result->getLimit() + (int) ((string) $hits['limit']));
        $result->setMaxScore((int) $hits['maxScore']);
        $result->setTotalHits((int) $hits['totalHits']);
        
        foreach ($hits->children() as $object) {
            if (isset($object['absoluteScore'])) {
                $result->addObject((int) $object['id'], (float) $object['absoluteScore']);
            } else {
                $result->addObject((int) $object['id'], (float) $object->Item[0]['absoluteScore']);
            }
        }
        return $result;
    }
}
