<?php declare(strict_types=1);

/* Copyright (c) 1998-2009 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * external link presentation bridge
 * @author  Stefan Meyer <meyer@leifos.com>
 * @ingroup ServicesADT
 */
class ilADTExternalLinkPresentationBridge extends ilADTPresentationBridge
{

    /**
     * Is valid type
     * @param ilADT $a_adt
     * @return bool
     */
    protected function isValidADT(ilADT $a_adt) : bool
    {
        return $a_adt instanceof ilADTExternalLink;
    }

    /**
     * Get html
     * @return string
     */
    public function getHTML() : string
    {
        if ($this->getADT()->isNull()) {
            return '';
        }
        if (!strlen($this->getADT()->getTitle())) {
            $presentation_value = $this->getADT()->getUrl();
            $presentation_clickable = ilUtil::makeClickable($presentation_value);
            return $this->decorate($presentation_clickable);
        }

        return $this->decorate(
            '<a target="_blank" href="' . $this->getADT()->getUrl() . '">' . $this->getADT()->getTitle() . '</a>'
        );
    }

    /**
     * Get soratable
     * @return mixed
     */
    public function getSortable() : mixed
    {
        if (!$this->getADT()->isNull()) {
            return $this->getADT()->getUrl();
        }
        return '';
    }
}
