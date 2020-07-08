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
        if(DateTime::createFromFormat(DtISO8601, "$from") !== FALSE)
        {
            $DateImmutable = new \DateTimeImmutable("$from");
            return $DateImmutable->format(DtISO8601);
        }
        elseif(true === is_int($from))
        {
            return $UnixTimestamp = strtotime($from);
        }
        else
        {

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