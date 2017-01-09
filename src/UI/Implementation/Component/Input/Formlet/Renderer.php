<?php

/* Copyright (c) 2016 Timon Amstutz <timon.amstutz@ilub.unibe.ch> Extended GPL, see docs/LICENSE */

namespace ILIAS\UI\Implementation\Component\Input\Formlet;

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
		 * @var \ILIAS\UI\Implementation\Component\Input\Formlet\Formlet $component
		 */
        $content = "";
        foreach($component->extractToView() as $item){
            $content .= $default_renderer->render($item);
        }
        return $content;
	}

	/**
	 * @inheritdocs
	 */
	protected function getComponentInterfaceName() {
		return [Component\Input\Item\Formlet\Formlet::class];
	}
}
