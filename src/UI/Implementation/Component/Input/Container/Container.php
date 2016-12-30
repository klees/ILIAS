<?php

namespace ILIAS\UI\Implementation\Component\Input\Container;

use ILIAS\UI\Implementation\Component\ComponentHelper;
use ILIAS\UI\Implementation\Component\Input\Item as I;
use ILIAS\UI\Implementation\Component\Input\ValidationMessageCollector;

/**
 * One item in the filter, might be composed from different input elements,
 * which all act as one filter input.
 */
class Container  implements \ILIAS\UI\Component\Input\Container\Container{
	use ComponentHelper;

	/**
	 * @var \ILIAS\UI\Component\Input\Item\Item[]
	 */
	protected $items = [];


    /**
     * @var ValidationMessageCollector
     */
    protected $collector = null;

	/**
	 * @inheritdoc
	 */
	public function __construct($items) {
		$items = $this->toArray($items);
		$types = [I\Item::class];
		$this->checkArgListElements("items", $items, $types);

        $this->collector = new \ILIAS\UI\Implementation\Component\Input\Validation\ValidationMessageCollector();

		/**
		 * TODO: Is this a good construct, do items need keys. Do they need to know about their key? If yes, who
		 * should set the key, consumer or the form?
		 */
		foreach($items as $item){
			if (array_key_exists($item->title(),$this->items)) {
				throw new \InvalidArgumentException("Argument Items '".$item->title()."': Duplicate title used for inputs in form");
			}
			$this->items[$item->title()] = $item;
		}
	}

	public function getItems(){
		return $this->items;
	}

	public function withPostInput(){
		$this->withInput($_POST);
        return $this;
	}
	/**
	 * @inheritdoc
	 */
	public function withInput(array $input = null){
		foreach($input as $key => $value){
			if(array_key_exists($key, $this->items)){
                $this->items[$key] = $this->items[$key]->withContent($value);
			}else{
                //Todo throw error, what about Post?
            }
		}
        return $this;
	}

	/**
	 * @inheritdoc
     * TODO wrong method here, change this
	 */
	public function hasValidContent(){
        //Todo this really should be cached
        $valid = true;
        foreach($this->items as $item){
            if(!$item->validate($this->collector)){
                $valid = false;
            }
        }
        return $valid;
	}

	/**
	 * @inheritdoc
	 */
	public function content(){
		$content = [];

        if(!$this->hasValidContent($this->collector)){
            //Todo improve error handling here
            throw new \Exception("Bad Content",$this->collector);
        }
		foreach($this->items as $item){
			$content[] = $item->content();
		}
		return $content;
	}


	/**
	 * @inheritdoc
	 */
	public function validationErrors(){
        return $this->collector;
	}

}
