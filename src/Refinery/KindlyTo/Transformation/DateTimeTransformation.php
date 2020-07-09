<?php
/* Copyright (c) 1998-2020 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * @author Luka Stocker <lstocker@concepts-and-training.de>
 */

namespace ILIAS\Refinery\KindlyTo\Transformation;

use ILIAS\Data\Result;
use ILIAS\Refinery\DeriveApplyToFromTransform;
use ILIAS\Refinery\Transformation;

/**
 * Set date format and RegExr constants.
 * Constants with the same date formats are not set more then once:
 * - RFC3339 & W3C same as Atom
 * - RFC850 same as Cookie
 * - RFC1036, RFC1123, RFC2822same & RSS same as RFC822
 */
class DateTimeTransformation implements Transformation
{
    const Dt_Atom = 'Y-m-d\TH:i:sP';
    const Reg_Atom = '/^([0-9]{4})-([0-9]{2})-([0-9]{2})T([0-9]{2}):([0-9]{2}):([0-9]{2})\+([0-9]{2}):([0-9]{2})$/';
    const Dt_Cookie = 'l, d-M-Y H:i:s T';
    const Reg_Cookie = '/^([A-Za-z]+),\ ([0-9]{2})-([A-Z][a-z]+)-([0-9]{4})\ ([0-9]{2}):([0-9]{2}):([0-9]{2})\ ([A-Za-z]+)\+([0-9]{4})$/';
    const Dt_ISO8601 = 'Y-m-d\TH:i:sO';
    const Reg_ISO8601 = '/^([0-9]{4})-([0-9]{2})-([0-9]{2})T([0-9]{2}):([0-9]{2}):([0-9]{2})\+([0-9]{4})$/';
    const Dt_RFC822 = 'D, d M y H:i:s O';
    const Reg_RFC822 = '/^([A-Za-z]+),\ ([0-9]{2})\ ([A-Z][a-z]+)\ ([0-9]{2})\ ([0-9]{2}):([0-9]{2}):([0-9]{2})\ \+([0-9]{4})$/';
    const Dt_RFC7231 = 'D, d M Y H:i:s \G\M\T';
    const Reg_RFC7231 = '/^([A-Za-z]+),\ ([0-9]{2})\ ([A-Za-z]+)\ ([0-9]{4})\ ([0-9]{2}):([0-9]{2}):([0-9]{2})\ ([A-Za-z]+)$/';
    const Dt_RFC3339_ext = 'Y-m-d\TH:i:s.vP';
    const Reg_RFC3339_ext = '/^([0-9]{4})-([0-9]{2})-([0-9]{2})T([0-9]{2}):([0-9]{2}):([0-9]{2})\.([0-9]{3})\+([0-9]{2}):([0-9]{2})$/';

    use DeriveApplyToFromTransform;

    /**
     * @inheritdoc
     */
    public function transform($from)
    {
        if(TRUE === is_string($from))
        {
            if(preg_match(self::Reg_Atom, $from, $RegMatch))
            {
                $from = strval($from);
                $DateImmutable = new \DateTimeImmutable($from);
                return $DateImmutable->format(self::Dt_Atom);
            }
            elseif(preg_match(self::Reg_Cookie, $from, $RegMatch))
            {
                $from = strval($from);
                $DateImmutable = new \DateTimeImmutable($from);
                return $DateImmutable->format(self::Dt_Cookie);
            }
            elseif(preg_match(self::Reg_ISO8601,$from,$RegMatch))
            {
                $from = strval($from);
                $DateImmutable = new \DateTimeImmutable($from);
                return $DateImmutable->format(self::Dt_ISO8601);
            }
            elseif(preg_match(self::Reg_RFC822,$from,$RegMatch))
            {
                $from = strval($from);
                $DateImmutable = new \DateTimeImmutable($from);
                return $DateImmutable->format(self::Dt_RFC822);
            }
            elseif(preg_match(self::Reg_RFC7231,$from,$RegMatch))
            {
                $from = strval($from);
                $DateImmutable = new \DateTimeImmutable($from);
                return $DateImmutable->format(self::Dt_RFC7231);
            }
            elseif(preg_match(self::Reg_RFC3339_ext,$from,$RegMatch))
            {
                $from = strval($from);
                $DateImmutable = new \DateTimeImmutable($from);
                return $DateImmutable->format(self::Dt_RFC3339_ext);
            }
        }
        elseif(true === is_int($from) || true === is_float($from))
        {
            return $UnixTimestamp = strtotime($from);
        }
        else
        {
            throw new \InvalidArgumentException("$from can not be transformed into DateTimeImmutable or Unix timestamp.", 1);
        }

    }

    /**
     * @inheritdoc
     */
    public function applyTo(Result $data): Result
    {
    }

    /**
     * @inheritdoc
     */
    public function __invoke($from)
    {
        return $this->transform($from);
    }
}