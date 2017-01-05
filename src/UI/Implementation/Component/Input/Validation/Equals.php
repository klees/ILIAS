<?php
namespace ILIAS\UI\Implementation\Component\Input\Validation;


/**
 * Todo
 */
class Equals extends Validation implements
	\ILIAS\UI\Component\Input\Validation\Equals {

	/**
	 * @inheritdoc
	 */
	public function __construct($to_be_equaled) {
		$function = function($input) use($to_be_equaled){
			return $to_be_equaled == $input;
		};

		parent::__construct($function,"Must equal ".$to_be_equaled);
	}

}