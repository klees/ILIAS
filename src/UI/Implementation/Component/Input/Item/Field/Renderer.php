<?php

/* Copyright (c) 2016 Timon Amstutz <timon.amstutz@ilub.unibe.ch> Extended GPL, see docs/LICENSE */

namespace ILIAS\UI\Implementation\Component\Input\Item\Field;

use ILIAS\UI\Implementation\Render\AbstractComponentRenderer;
use ILIAS\UI\Renderer as RendererInterface;
use ILIAS\UI\Component;

/**
 * Class Renderer
 * @package ILIAS\UI\Implementation\Component\Image
 */
class Renderer extends AbstractComponentRenderer {
	/**
	 * @inheritdocs
	 */
	public function render(Component\Component $component, RendererInterface $default_renderer) {
		/**
		 * @var \ILIAS\UI\Implementation\Component\Input\Item\Item $component
		 */
		$this->checkComponent($component);
		$tpl = $this->getTemplate("Filter/tpl.text.html", true, true);

		if($component instanceof NameAge)
		{
			$inputs ="";
			foreach($component->extractToView() as $option){
				$inputs .= $default_renderer->render($option);
			}
			return $inputs;
		}else{
			//$tpl->setVariable("ID","TODO");
			$tpl->setVariable("FOR",$component->getId());
			$tpl->setVariable("ID",$component->getId());
			$tpl->setVariable("VALUE",$component->extractToView());


			if($component->isValidated()){
				if($component->isValid()){
					$tpl->touchBlock("success");
				}else{
					$tpl->touchBlock("error");
					$tpl->setVariable("VALIDATION_ERROR",
							$component->getMessageCollector()->getMessages()[0]->getMessage());
				}

			}


			$tpl->setVariable("LABEL",$component->getLabel());

			if($component->isRequired()){
				$tpl->setVariable("REQUIRED","required");
			}else{
				$tpl->setVariable("REQUIRED","");
			}

			return $tpl->get();
		}

	}

	/**
	 * @inheritdocs
	 */
	protected function getComponentInterfaceName() {
		return [Component\Input\Item\Field\Text::class];
	}
}
