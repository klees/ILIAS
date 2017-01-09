<?php

namespace ILIAS\UI\Implementation\Component\Input\Container;

use ILIAS\UI\Implementation\Component\ComponentHelper;
use ILIAS\UI\Implementation\Component\Input\Item as I;
use ILIAS\UI\Implementation\Component\Input\ValidationMessageCollector;
use \ILIAS\UI\Implementation\Component\Input\Formlet as F;

/**
 * One item in the filter, might be composed from different input elements,
 * which all act as one filter input.
 */
class Container extends F\Formlet implements
		\ILIAS\UI\Component\Input\Container\Container{
	use ComponentHelper;


}
