<?php

/**
 * Class ilBiblLibraryPresentationGUI
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class ilBiblLibraryPresentationGUI
{
    
    protected \ilBiblLibraryInterface $library;
    protected \ilBiblFactoryFacade $facade;
    
    /**
     * ilBiblLibraryPresentationGUI constructor.
     * @param \ilBiblLibraryInterface $library
     */
    public function __construct(\ilBiblLibraryInterface $library, \ilBiblFactoryFacade $facade)
    {
        $this->library = $library;
        $this->facade  = $facade;
    }
    
    /**
     * @param              $type
     * @deprecated REFACTOR Mit Attribute Objekten arbeiten statt mit Array. Evtl. URL Erstellung vereinfachen
     */
    public function generateLibraryLink(ilBiblEntry $entry, $type) : string
    {
        $attributes = $this->facade->entryFactory()->loadParsedAttributesByEntryId($entry->getId());
        $type       = $this->facade->typeFactory()->getInstanceForString($type);
        $attr       = [];
        $prefix     = '';
        switch ($type->getId()) {
            case ilBiblTypeFactoryInterface::DATA_TYPE_BIBTEX:
                $prefix = "bib_default_";
                if (!empty($attributes[$prefix . "isbn"])) {
                    $attr = array("isbn");
                } elseif (!empty($attributes[$prefix . "pmid"])) {
                    $attr = array("pmid");
                } elseif (!empty($attributes[$prefix . "doi"])) {
                    $attr = array("doi");
                } elseif (!empty($attributes[$prefix . "issn"])) {
                    $attr = array("issn");
                } else {
                    $attr = array("title", "author", "year", "number", "volume");
                }
                break;
            case ilBiblTypeFactoryInterface::DATA_TYPE_RIS:
                $prefix = "ris_" . strtolower($entry->getType()) . "_";
                if (!empty($attributes[$prefix . "sn"])) {
                    $attr = array("sn");
                } elseif (!empty($attributes[$prefix . "do"])) {
                    $attr = array("do");
                } else {
                    $attr = array("ti", "t1", "au", "py", "is", "vl");
                }
                break;
        }
        
        $url_params = "?";
        if (count($attr) === 1) {
            if (($attr[0] === "doi") || ($attr[0] === "pmid")) {
                $url_params .= "id=" . $this->formatAttribute($attr[0], $type, $attributes, $prefix)
                    . "%3A" . $attributes[$prefix . $attr[0]];
            } elseif ($attr[0] === "do") {
                $url_params .= "id=" . $this->formatAttribute($attr[0], $type, $attributes, $prefix)
                    . "i%3A" . $attributes[$prefix . $attr[0]];
            } else {
                $url_params .= $this->formatAttribute($attr[0], $type, $attributes, $prefix) . "="
                    . urlencode($attributes[$prefix . $attr[0]]);
            }
        } else {
            foreach ($attr as $a) {
                if (array_key_exists($prefix . $a, $attributes)) {
                    if (strlen($url_params) > 1) {
                        $url_params .= "&";
                    }
                    $url_params .= $this->formatAttribute($a, $type, $attributes, $prefix) . "="
                        . urlencode($attributes[$prefix . $a]);
                }
            }
        }
        
        return $this->library->getUrl() . $url_params;
    }
    
    /**
     * @param \ilBiblFactoryFacadeInterface $bibl_factory_facade
     * @param \ilBiblEntry                  $entry
     * @return string|void
     */
    public function getButton(ilBiblFactoryFacadeInterface $bibl_factory_facade, ilBiblEntry $entry)
    {
        if ($this->library->getImg()) {
            $button = ilImageLinkButton::getInstance();
            $button->setUrl($this->generateLibraryLink($entry, $bibl_factory_facade->type()->getStringRepresentation()));
            $button->setImage($this->library->getImg(), false);
            $button->setTarget('_blank');
            
            return $button->render();
        } else {
            $button = ilLinkButton::getInstance();
            $button->setUrl($this->generateLibraryLink($entry, $bibl_factory_facade->type()->getStringRepresentation()));
            $button->setTarget('_blank');
            $button->setCaption('bibl_link_online');
            
            return $button->render();
        }
    }
    
    /**
     * @deprecated REFACTOR type via type factory verwenden
     */
    public function formatAttribute(string $a, ilBiblTypeInterface $type, array $attributes, string $prefix) : string
    {
        if ($type->getStringRepresentation() === 'ris') {
            switch ($a) {
                case 't1':
                case 'ti':
                    $a = "title";
                    break;
                case 'au':
                    $a = "author";
                    break;
                case 'sn':
                    if (strlen($attributes[$prefix . "sn"]) <= 9) {
                        $a = "issn";
                    } else {
                        $a = "isbn";
                    }
                    break;
                case 'py':
                    $a = "date";
                    break;
                case 'is':
                    $a = "issue";
                    break;
                case 'vl':
                    $a = "volume";
                    break;
            }
        } elseif ($type->getStringRepresentation() === 'bib') {
            switch ($a) {
                case 'number':
                    $a = "issue";
                    break;
                case 'year':
                    $a = "date";
                    break;
            }
        }
        
        return $a;
    }
}
