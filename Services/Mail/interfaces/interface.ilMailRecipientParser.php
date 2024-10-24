<?php declare(strict_types=1);
/* Copyright (c) 1998-2021 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * Interface ilMailRecipientParser
 * @author Michael Jansen <mjansen@databay.de>
 */
interface ilMailRecipientParser
{
    /**
     * @return ilMailAddress[]
     */
    public function parse() : array;
}
