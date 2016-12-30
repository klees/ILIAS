<?php

/* Copyright (c) 2016 Timon Amstutz <timon.amstutz@ilub.unibe.ch> Extended GPL, see docs/LICENSE */

namespace  ILIAS\UI\Implementation\Component\Input\Container\Form;

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
		 * @var Component\Input\Container\Container $component
		 */
		$this->checkComponent($component);
		$tpl = $this->getTemplate("Form/tpl.standard.html", true, true);

		$content = "";
		foreach($component->getChildren() as $item){
			$content .= $default_renderer->render($item);
		}
		$tpl->setVariable("CONTENT",$content);
		$tpl->setVariable("ACTION",$component->getAction());
		/**
		if($component->isRequired()){
			$tpl->touchBlock("required");
			$tpl->setVariable("REQUIRED","true");

		}**/

		return $tpl->get();
	}

	/**
	 * @inheritdocs
	 */
	protected function getComponentInterfaceName() {
		return [Component\Input\Container\Form\Standard::class];
	}
}