<?php
/* Copyright (c) 1998-2013 ILIAS open source, Extended GPL, see docs/LICENSE */

require_once('./Services/COPage/classes/class.ilPageObject.php');

/**
 * Question page object
 *
 * @author Alex Killing <alex.killing@gmx.de>
 *
 * @version $Id$
 *
 * @ingroup ModulesTestQuestionPool
 */
class ilAssQuestionPage extends ilPageObject
{
    /**
     * Get parent type
     * @return string parent type
     */
    public function getParentType() : string
    {
        return "qpl";
    }
}
