<?php declare(strict_types=1);

/* Copyright (c) 2022 Thibeau Fuhrer <thibeau@sr.solutions> Extended GPL, see docs/LICENSE */

namespace ILIAS\UI\Implementation\Component\Input;

use Iterator;

/**
 * @author Thibeau Fuhrer <thibeau@sr.solutions>
 */
class DynamicInputDataIterator implements Iterator
{
    protected string $parent_input_name;
    protected array $post_data;
    protected int $index = 0;

    public function __construct(InputData $data, string $parent_input_name)
    {
        $this->post_data = $data->getOr($parent_input_name, []);
        $this->parent_input_name = $parent_input_name;
    }

    public function current() : ?InputData
    {
        if ($this->valid()) {
            $entry = [];
            // for each input of the dynamic input template, the input data must
            // be mapped to the rendered name, similar to one delivered by
            // DynamicInputsNameSource for further processing.
            foreach ($this->post_data as $input_name => $data) {
                $dynamic_input_name = "$this->parent_input_name[$input_name][]";
                $entry[$dynamic_input_name] = $data[$this->index];
            }

            return new ArrayInputData($entry);
        }

        return null;
    }

    public function next() : void
    {
        $this->index++;
    }

    public function key() : ?int
    {
        if ($this->valid()) {
            return $this->index;
        }

        return null;
    }

    public function valid() : bool
    {
        if (empty($this->post_data)) {
            return false;
        }

        foreach ($this->post_data as $input_data) {
            if (!isset($input_data[$this->index])) {
                return false;
            }
        }

        return true;
    }

    public function rewind() : void
    {
        $this->index = 0;
    }
}
