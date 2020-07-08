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
const DtAtom = 'Y-m-d\TH:i:sP';
const RegAtom = '/^([0-9]{4})-([0-9]{2})-([0-9]{2})T([0-9]{2}):([0-9]{2}):([0-9]{2})\+([0-9]{2}):([0-9]{2})$/';
const DtCookie = 'l, d-M-Y H:i:s T';
const RegCookie = '/^([A-Za-z]+),\ ([0-9]{2})-([A-Z][a-z]+)-([0-9]{4})\ ([0-9]{2}):([0-9]{2}):([0-9]{2})\ ([A-Za-z]+)\+([0-9]{4})$/';
const DtISO8601 = 'Y-m-d\TH:i:sO';
const RegISO8601 = '/^([0-9]{4})-([0-9]{2})-([0-9]{2})T([0-9]{2}):([0-9]{2}):([0-9]{2})\+([0-9]{4})$/';
const DtRFC822 = 'D, d M y H:i:s O';
const RegRFC822 = '/^([A-Za-z]+),\ ([0-9]{2})\ ([A-Z][a-z]+)\ ([0-9]{2})\ ([0-9]{2}):([0-9]{2}):([0-9]{2})\ \+([0-9]{4})$/';
const DtRFC7231 = 'D, d M Y H:i:s \G\M\T';
const RegRFC7231 = '/^([A-Za-z]+),\ ([0-9]{2})\ ([A-Za-z]+)\ ([0-9]{4})\ ([0-9]{2}):([0-9]{2}):([0-9]{2})\ ([A-Za-z]+)$/';
const DtRFC3339ext = 'Y-m-d\TH:i:s.vP';
const RegRFC3339ext = '/^([0-9]{4})-([0-9]{2})-([0-9]{2})T([0-9]{2}):([0-9]{2}):([0-9]{2})\.([0-9]{3})\+([0-9]{2}):([0-9]{2})$/';

class DateTimeTransformation implements Transformation
{
    use DeriveApplyToFromTransform;

    /**
     * @inheritdoc
     */
    public function transform($from)
    {
        if(TRUE === is_string($from))
        {
            if(preg_match(RegAtom, $from, $RegMatch))
            {
                $from = strval($from);
                $DateImmutable = new \DateTimeImmutable($from);
                return $DateImmutable->format(DtAtom);
            }
            elseif(preg_match(RegCookie, $from, $RegMatch))
            {
                $from = strval($from);
                $DateImmutable = new \DateTimeImmutable($from);
                return $DateImmutable->format(DtCookie);
            }
            elseif(preg_match(RegISO8601,$from,$RegMatch))
            {
                $from = strval($from);
                $DateImmutable = new \DateTimeImmutable($from);
                return $DateImmutable->format(DtISO8601);
            }
            elseif(preg_match(RegRFC822,$from,$RegMatch))
            {
                $from = strval($from);
                $DateImmutable = new \DateTimeImmutable($from);
                return $DateImmutable->format(DtRFC822);
            }
            elseif(preg_match(RegRFC7231,$from,$RegMatch))
            {
                $from = strval($from);
                $DateImmutable = new \DateTimeImmutable($from);
                return $DateImmutable->format(DtRFC7231);
            }
            elseif(preg_match(RegRFC3339ext,$from,$RegMatch))
            {
                $from = strval($from);
                $DateImmutable = new \DateTimeImmutable($from);
                return $DateImmutable->format(DtRFC3339ext);
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