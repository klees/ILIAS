<?php declare(strict_types=1);
/* Copyright (c) 1998-2018 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * Interface ilTermsOfServiceEquatable
 * @author Michael Jansen <mjansen@databay.de>
 */
interface ilTermsOfServiceEquatable
{
    public function equals($other) : bool;
}
