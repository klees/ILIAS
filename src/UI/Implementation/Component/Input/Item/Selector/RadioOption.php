<?php

namespace ILIAS\UI\Implementation\Component\Input\Item\Selector;

use ILIAS\UI\Component\Input\Item\Selector as S;
use ILIAS\UI\Implementation\Component\Input\Item as I;

/**
 * Class Radio
 * Todo this is mostly experimenting
 * @package ILIAS\UI\Implementation\Component\Input\Item\Field
 */
class RadioOption extends I\Item implements S\RadioOption {

    protected $group_id = "";

    public function inGroup($id){
        $this->group_id = $id;
    }

    public function getGroup(){
        return $this->group_id;
    }

}
