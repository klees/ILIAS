<?php

namespace ILIAS\UI\Implementation\Component\Input\Validation;


/**
 * Class Factory
 *
 * @package ILIAS\UI\Implementation\Component\Filter
 */
class ValidationMessageCollector implements \ILIAS\UI\Component\Input\Validation\ValidationMessageCollector {

    /**
     * @var ValidationMessage[]
     */
    protected $messages = [];

    public function withMessage(\ILIAS\UI\Component\Input\Validation\ValidationMessage $message){
        $this->messages[] = $message;
    }

    public function getMessages(){
        return $this->message;
    }

    public function join(\ILIAS\UI\Component\Input\Validation\ValidationMessageCollector $collector){
        $this->messages = array_merge($this->message,$collector->getMessages());
    }

    /**
     * Iterator implementations
     *
     * @return bool
     */
    public function valid() {
        return current($this->messages) !== false;
    }

    public function key() {
        return key($this->messages);
    }

    public function current() {
        return current($this->messages);
    }

    public function next() {
        next($this->messages);
    }
    public function rewind() {
        reset($this->messages);
    }
}