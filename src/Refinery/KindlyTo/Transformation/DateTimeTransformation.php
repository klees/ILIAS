<?php
/* Copyright (c) 1998-2020 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * @author Luka Stocker <lstocker@concepts-and-training.de>
 */

namespace ILIAS\Refinery\KindlyTo\Transformation;

use DateTime;
use Exception;
use ILIAS\Data\Result;
use ILIAS\Refinery\DeriveApplyToFromTransform;
use ILIAS\Refinery\Transformation;
/** use ILIAS\Refinery\ConstraintViolationException; */

const DtAtom = 'Y-m-d\TH:i:sP';
const DtCookie = 'l, d-M-Y H:i:s T';
const DtISO8601 = 'Y-m-d\TH:i:sO';
const DtRFC822 = 'D, d M y H:i:s O';
const DtRFC850 = 'l, d-M-y H:i:s T';
const DtRFC1036 = 'D, d M y H:i:s O';
const DtRFC1123 = 'D, d M y H:i:s O';
const DtRFC7231 = 'D, d M Y H:i:s \G\M\T';
const DtRFC2822 = 'D, d M Y H:i:s O';
const DtRFC3339 = 'Y-m-d\TH:i:sP';
const DtRFC3339ext = 'Y-m-d\TH:i:s.vP';
const DtRSS = 'D, d M Y H:i:s O';
const DtW3C = 'Y-m-d\TH:i:sP';

class DateTimeTransformation implements Transformation
{
    use DeriveApplyToFromTransform;

    /**
     * @inheritdoc
     */
    public function transform($from)
    {

        if(DateTime::createFromFormat(DtAtom, $from) !== FALSE)
        {
            $from = strval($from);
            $DateImmutable = new \DateTimeImmutable($from);
            return $DateImmutable->format(DtAtom);
        }
        elseif(DateTime::createFromFormat(DtCookie, $from) !== FALSE)
        {
            $from = strval($from);
            $DateImmutable = new \DateTimeImmutable($from);
            return $DateImmutable->format(DtCookie);
        }
        elseif(DateTime::createFromFormat(DtISO8601, $from) !== FALSE)
        {
            $from = strval($from);
            $DateImmutable = new \DateTimeImmutable($from);
            return $DateImmutable->format(DtISO8601);
        }
        elseif(DateTime::createFromFormat(DtRFC822, $from) !== FALSE)
        {
            $from = strval($from);
            $DateImmutable = new \DateTimeImmutable($from);
            return $DateImmutable->format(DtRFC822);
        }
        elseif(DateTime::createFromFormat(DtRFC850, $from) !== FALSE)
        {
            $from = strval($from);
            $DateImmutable = new \DateTimeImmutable($from);
            return $DateImmutable->format(DtRFC850);
        }
        elseif(DateTime::createFromFormat(DtRFC1036, $from) !== FALSE)
        {
            $from = strval($from);
            $DateImmutable = new \DateTimeImmutable($from);
            return $DateImmutable->format(DtRFC1036);
        }
        elseif(DateTime::createFromFormat(DtRFC1123, $from) !== FALSE)
        {
            $from = strval($from);
            $DateImmutable = new \DateTimeImmutable($from);
            return $DateImmutable->format(DtRFC1123);
        }
        elseif(DateTime::createFromFormat(DtRFC7231, $from) !== FALSE)
        {
            $from = strval($from);
            $DateImmutable = new \DateTimeImmutable($from);
            return $DateImmutable->format(DtRFC7231);
        }
        elseif(DateTime::createFromFormat(DtRFC2822, $from) !== FALSE)
        {
            $from = strval($from);
            $DateImmutable = new \DateTimeImmutable($from);
            return $DateImmutable->format(DtRFC2822);
        }
        elseif(DateTime::createFromFormat(DtRFC3339, $from) !== FALSE)
        {
            $from = strval($from);
            $DateImmutable = new \DateTimeImmutable($from);
            return $DateImmutable->format(DtRFC3339);
        }
        elseif(DateTime::createFromFormat(DtRFC3339ext, $from) !== FALSE)
        {
            $from = strval($from);
            $DateImmutable = new \DateTimeImmutable($from);
            return $DateImmutable->format(DtRFC3339ext);
        }
        elseif(DateTime::createFromFormat(DtRSS, $from) !== FALSE)
        {
            $from = strval($from);
            $DateImmutable = new \DateTimeImmutable($from);
            return $DateImmutable->format(DtRSS);
        }
        elseif(DateTime::createFromFormat(DtW3C, $from) !== FALSE)
        {
            $from = strval($from);
            $DateImmutable = new \DateTimeImmutable($from);
            return $DateImmutable->format(DtW3C);
        }
        elseif(true === is_int($from))
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