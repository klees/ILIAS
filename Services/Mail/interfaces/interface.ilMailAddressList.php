<?php declare(strict_types=1);
/* Copyright (c) 1998-2021 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * Interface ilMailAddressList
 * @author Michael Jansen <mjansen@databay.de>
 */
interface ilMailAddressList
{
    /**
     * @return ilMailAddress[]
     */
    public function value() : array;
}
