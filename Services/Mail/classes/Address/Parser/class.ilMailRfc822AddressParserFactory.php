<?php declare(strict_types=1);
/* Copyright (c) 1998-2021 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * Class ilMailRfc822AddressParserFactory
 * @author Michael Jansen <mjansen@databay.de>
 */
class ilMailRfc822AddressParserFactory
{
    public function getParser(string $address) : ilMailRecipientParser
    {
        return new ilMailRfc822AddressParser(new ilMailPearRfc822WrapperAddressParser($address));
    }
}
