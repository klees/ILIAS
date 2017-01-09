<?php

/* Copyright (c) 2016 Amstutz Timon <timon.amstutz@ilub.unibe.ch> Extended GPL, see docs/LICENSE */

namespace ILIAS\UI\Implementation\Component\Input\Item\Selector;

use ILIAS\UI\Component\Input\Item\Selector as S;

/**
 * Class Factory
 *
 * @package ILIAS\UI\Implementation\Component\Filter
 */
class Factory implements S\Factory {

    public function repository(){

    }

    public function radioGroup($id,$label,$radio_options){
        return new RadioGroup($id,$label,$radio_options);

    }

    /**
     * ---
     * description:
     *   purpose: >
     *     Todo
     *
     * ----
     * @param RadioOption[] $radio_options Radio options to be offered by the radio Group
     * @return  \ILIAS\UI\Component\Input\Item\Selector\RadioGroup
     */
    public function radioOption($id,$label){
        return new RadioOption($id,$label);
    }
}