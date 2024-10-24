<?php declare(strict_types=1);

/* Copyright (c) 2019 Nils Haagen <nils.haagen@concepts-and-training.de> Extended GPL, see docs/LICENSE */

namespace ILIAS\UI\Component\Input\Field;

use ILIAS\Data\DateFormat\DateFormat;
use ILIAS\UI\Component\Component;
use DateTimeImmutable;

/**
 * This describes the datetime-field.
 */
interface DateTime extends FormInput
{
    /**
     * Get an input like this using the given format.
     */
    public function withFormat(DateFormat $format) : DateTime;

    /**
     * Get the date-format of this input.
     */
    public function getFormat() : DateFormat;

    /**
     * Get an input like this using the given timezone.
     */
    public function withTimezone(string $tz) : DateTime;

    /**
     * Get the timezone of this input.
     */
    public function getTimezone() : ?string;

    /**
     * Limit accepted values to datetime past (and including) the given $datetime.
     */
    public function withMinValue(DateTimeImmutable $datetime) : DateTime;

    /**
     * Return the lowest value the input accepts.
     */
    public function getMinValue() : ?DateTimeImmutable;

    /**
     * Limit accepted values to datetime before (and including) the given value.
     */
    public function withMaxValue(DateTimeImmutable $datetime) : DateTime;

    /**
     * Return the maximum date the input accepts.
     */
    public function getMaxValue() : ?DateTimeImmutable;

    /**
     * Input both date and time.
     */
    public function withUseTime(bool $with_time) : DateTime;

    /**
     * Should the input be used to get both date and time?
     */
    public function getUseTime() : bool;

    /**
     * Use this Input for a time-value rather than a date.
     */
    public function withTimeOnly(bool $time_only) : DateTime;

    /**
     * Should the input be used to get a time only?
     */
    public function getTimeOnly() : bool;
}
