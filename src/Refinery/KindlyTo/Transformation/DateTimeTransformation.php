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
    const Reg_Atom = '/^([0-9]{4})([-])([0-9]{2})([-])([0-9]{2})([T])([0-9]{2})([:])([0-9]{2})([:])([0-9]{2})([\)([+])([0-9]{2})([:])([0-9]{2})$/';
    const Reg_Cookie = '/^([A-Za-z]+)([,])([\])([ ])([0-9]{2})([-])([A-Z][a-z]+)([-])([0-9]{4})([\])([ ])([0-9]{2})([:])([0-9]{2})([:])([0-9]{2})([\])([ ])([A-Za-z]+)([\])([+])([0-9]{4})$/';
    const Reg_ISO8601 = '/^([0-9]{4})([-])([0-9]{2})([-])([0-9]{2})([T])([0-9]{2})([:])([0-9]{2})([:])([0-9]{2})([+])([0-9]{4})$/';
    const Reg_RFC822 = '/^([A-Za-z]+)([,])([\])([ ])([0-9]{2})([\])([ ])([A-Z][a-z]+)([\])([ ])([0-9]{2})([\])([ ])([0-9]{2}):([0-9]{2})([:])([0-9]{2})([\])([ ])([\])([+])([0-9]{4})$/';
    const Reg_RFC7231 = '/^([A-Za-z]+)([,])([\])([ ])([0-9]{2})([\])([ ])([A-Za-z]+)([\])([ ])([0-9]{4})([\])([ ])([0-9]{2})([:])([0-9]{2})([:])([0-9]{2})([\])([ ])([A-Za-z]+)$/';
    const Reg_RFC3339_ext = '/^([0-9]{4})([-])([0-9]{2})([-])([0-9]{2})([T])([0-9]{2})([:])([0-9]{2})([:])([0-9]{2})([\])([.])([0-9]{3})([\])([+])([0-9]{2})([:])([0-9]{2})$/';

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
                $DateImmutable = \DateTimeImmutable::createFromFormat(\DateTimeImmutable::ATOM, $from);
                return $DateImmutable->format(\DateTimeImmutable::ATOM);
            }
            elseif(preg_match(self::Reg_Cookie, $from, $RegMatch))
            {
                $DateImmutable = \DateTimeImmutable::createFromFormat(\DateTimeImmutable::COOKIE, $from);
                return $DateImmutable->format(\DateTimeImmutable::COOKIE);
            }
            elseif(preg_match(self::Reg_ISO8601,$from,$RegMatch))
            {
                $DateImmutable = \DateTimeImmutable::createFromFormat(\DateTimeImmutable::ISO8601, $from);
                return $DateImmutable->format(\DateTimeImmutable::ISO8601);
            }
            elseif(preg_match(self::Reg_RFC822,$from,$RegMatch))
            {
                $DateImmutable = \DateTimeImmutable::createFromFormat(\DateTimeImmutable::RFC822, $from);
                return $DateImmutable->format(\DateTimeImmutable::RFC822);
            }
            elseif(preg_match(self::Reg_RFC7231,$from,$RegMatch))
            {
                $DateImmutable = \DateTimeImmutable::createFromFormat(\DateTimeImmutable::RFC7231, $from);
                return $DateImmutable->format(\DateTimeImmutable::RFC7231);
            }
            elseif(preg_match(self::Reg_RFC3339_ext,$from,$RegMatch))
            {
                $DateImmutable = \DateTimeImmutable::createFromFormat(\DateTimeImmutable::RFC3339_EXTENDED, $from);
                return $DateImmutable->format(\DateTimeImmutable::RFC3339_EXTENDED);
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
    public function __invoke($from)
    {
        return $this->transform($from);
    }
}