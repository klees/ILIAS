<?php

/* Copyright (c) 2016 Amstutz Timon <timon.amstutz@ilub.unibe.ch> Extended GPL, see docs/LICENSE */


namespace ILIAS\UI\Implementation\Component\Input\Item\Field;

use ILIAS\UI\Component\Input\Item\Field as F;
use ILIAS\UI\Implementation\Component\Input\Item as I;

/**
 * Class Text
 * @package ILIAS\UI\Implementation\Component\Input\Item\Field
 */
class Text extends I\Item implements F\Text {

	/**
	 * @var string
	 */
	protected $type = "text";

	/**
	 * @inheritdoc
	 */
	public function __construct($label) {
		parent::__construct($label);
	}
}
