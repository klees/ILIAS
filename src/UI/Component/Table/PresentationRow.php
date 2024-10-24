<?php declare(strict_types=1);

/* Copyright (c) 2017 Nils Haagen <nils.haagen@concepts-and-training.de> Extended GPL, see docs/LICENSE */

namespace ILIAS\UI\Component\Table;

use ILIAS\UI\Component\Triggerable;
use \ILIAS\UI\Component\Signal;
use ILIAS\UI\Component\Component;
use ILIAS\UI\Component\Listing\Descriptive;
use ILIAS\UI\Component\Button\Button;
use ILIAS\UI\Component\Dropdown\Dropdown;

/**
 * This describes a Row used in Presentation Table.
 * A row consists (potentially) of title, subtitle, important fields (in the
 * collapsed row) and further fields to be shown in the expanded row.
 */
interface PresentationRow extends Component, Triggerable
{
    /**
     * Get a row like this with the given headline.
     */
    public function withHeadline(string $headline) : PresentationRow;

    /**
     * Get a row like this with the given subheadline.
     */
    public function withSubheadline(string $subheadline) : PresentationRow;

    /**
     * Get a row like this with the record-fields and labels
     * to be shown in the collapsed row.
     *
     * @param array<string,string> 	$fields
     */
    public function withImportantFields(array $fields) : PresentationRow;

    /**
     * Get a row like this with a descriptive listing as content.
     */
    public function withContent(Descriptive $content) : PresentationRow;

    /**
     * Get a row like this with a headline for the field-list in the expanded row.
     */
    public function withFurtherFieldsHeadline(string $headline) : PresentationRow;

    /**
     * Get a row like this with the record-fields and labels to be shown
     * in the list of the expanded row.
     *
     * @param array<string,string> 	$fields
     */
    public function withFurtherFields(array $fields) : PresentationRow;

    /**
     * Get a row like this with a button or a dropdown for actions in the expanded row.
     *
     * @param Button|Dropdown $action
     */
    public function withAction($action) : PresentationRow;

    /**
     * Get the signal to expand the row.
     */
    public function getShowSignal() : Signal;

    /**
     * Get the signal to collapse the row.
     */
    public function getCloseSignal() : Signal;

    /**
     * Get the signal to toggle (expand/collapse) the row.
     */
    public function getToggleSignal() : Signal;
}
