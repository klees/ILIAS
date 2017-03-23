<?php

/* Copyright (c) 2016 Timon Amstutz <timon.amstutz@ilub.unibe.ch> Extended GPL, see docs/LICENSE */

namespace  ILIAS\UI\Implementation\Component\Input\Container\Form;

use ILIAS\UI\Implementation\Render\AbstractComponentRenderer;
use ILIAS\UI\Renderer as RendererInterface;
use ILIAS\UI\Component;

/**
 * Class Renderer
 * Todo this is mostly experimenting
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
        $tpl = null;

        if($component instanceof Standard){
            $default_content = "";
            $sections = "";

            $tpl = $this->getTemplate("Form/tpl.standard.html", true, true);


            foreach($component->extractToView() as $item){
                if($item instanceof Section){
                    $sections .= $default_renderer->render($item);
                }else {
                    if (!($item instanceof Component\Component)) {
                        var_dump($component->extractToView());
                        exit;

                    }

                    $default_content  .= $default_renderer->render($item);
                }
            }

            $tpl->setVariable("CONTENT_DEFAULT_SECTION",$default_content);
            $tpl->setVariable("ADDITIONAL_SECTIONS",$sections);
            $tpl->setVariable("ACTION",$component->getAction());
            $tpl->setVariable("FORM_TITLE",$component->getTitle());


        }else{
            if($component instanceof SECTION)
            {
                $tpl = $this->getTemplate("Form/tpl.section.html", true, true);
                $tpl->setVariable("SECTION_TITLE", $component->getTitle());
            }else
            {
                $tpl = $this->getTemplate("Form/tpl.sub.html", true, true);
            }


            $content = "";
            foreach($component->extractToView() as $item){
                $content .= $default_renderer->render($item);
            }


            $tpl->setVariable("CONTENT",$content);

        }

        return $tpl->get();


	}

	/**
	 * @inheritdocs
	 */
	protected function getComponentInterfaceName() {
		return [Component\Input\Container\Container::class];
	}
}