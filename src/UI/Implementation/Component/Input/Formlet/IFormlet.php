<?php
/******************************************************************************
 * This work is inspired/based on work by Richard Klees published under:
 *
 * "An implementation of the "Formlets"-abstraction in PHP.
 * Copyright (c) 2014 Richard Klees <richard.klees@rwth-aachen.de>
 *
 * This software is licensed under The MIT License. You should have received
 * a copy of the along with the code."
 *
 * See: https://github.com/lechimp-p/php-formlets
 */
/* Copyright (c) 2016 Timon Amstutz <timon.amstutz@ilub.unibe.ch> Extended GPL, see docs/LICENSE */
namespace  ILIAS\UI\Implementation\Component\Input\Formlet;

use \ILIAS\UI\Component\Input\Input;
/**
 * A formlet represents one part of a form. It can be combined with other formlets
 * to yield new formlets. Formlets are immutable, that is they can be reused in
 * as many places as liked. All methods return fresh Formlets instead of muting
 * the Formlets they are called upon.
 *
 * Todo: Rethink the name. The currently proposed "Formlet" seems to have shifted from
 * the initial concept of formlets.
 */
interface IFormlet extends Input{
	/**
	 * Internally used to get the content to be rendered by the renderer
	 *
	 * @return array
	 */
	public function extractToView();

	/**
	 * Todo: Improve this! What is name?
	 * @param string $name
	 * @return Input
	 */
	public function setName($name);

	/**
	 * Todo: Improve this! What is name?
	 * @return string
	 */
	public function getName();
}
