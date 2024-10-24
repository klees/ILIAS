<?php declare(strict_types=0);

/* Copyright (c) 1998-2013 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * (Course) learning objective page configuration
 * @author  Jörg Lützenkirchen <luetzenkirchen@leifos.com>
 * @ingroup ModulesCourse
 */
class ilLOPageConfig extends ilPageConfig
{
    /**
     * Init
     */
    public function init() : void
    {
        $this->setEnableInternalLinks(true);
        $this->setIntLinkHelpDefaultType("RepositoryItem");
        $this->setEnablePCType("FileList", false);
        $this->setEnablePCType("Map", true);
        $this->setEnablePCType("Resources", false);
        $this->setMultiLangSupport(false);
        $this->setSinglePageMode(true);
    }
}
