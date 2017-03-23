<?php

namespace ILIAS\UI\Implementation\Component\Input\Item\Selector;

use ILIAS\UI\Component\Input\Item\Selector as S;
use ILIAS\UI\Implementation\Component\Input\Item as I;

/**
 * Todo this is mostly experimenting
 * Class RadioGroup
 * @package ILIAS\UI\Implementation\Component\Input\Item\Field
 */
class RadioGroup extends I\Item implements S\RadioGroup {

    /**
     * @inheritdoc
     */
    public function __construct($id, $label,$children = []) {
        $children_copy = [];
        foreach($children as $child){
            //Todo, this is bad, since it injects children with knowledge
            // about parents
            //$children_copy = $child->inGroup($this->getId());
            $children_copy[] = $child;
        }
        parent::__construct($id,$label,$children_copy);
    }
}
