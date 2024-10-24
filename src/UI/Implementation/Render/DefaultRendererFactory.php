<?php declare(strict_types=1);

/* Copyright (c) 2017 Richard Klees <richard.klees@concepts-and-training.de> Extended GPL, see docs/LICENSE */

namespace ILIAS\UI\Implementation\Render;

use ILIAS\Refinery\Factory as Refinery;
use ILIAS\UI\Component\Component;
use ILIAS\UI\Factory as RootFactory;
use ilLanguage;

class DefaultRendererFactory implements RendererFactory
{
    protected RootFactory $ui_factory;
    protected TemplateFactory $tpl_factory;
    protected ilLanguage $lng;
    protected JavaScriptBinding $js_binding;
    protected Refinery $refinery;
    protected ImagePathResolver $image_path_resolver;

    public function __construct(
        RootFactory $ui_factory,
        TemplateFactory $tpl_factory,
        ilLanguage $lng,
        JavaScriptBinding $js_binding,
        Refinery $refinery,
        ImagePathResolver $image_path_resolver
    ) {
        $this->ui_factory = $ui_factory;
        $this->tpl_factory = $tpl_factory;
        $this->lng = $lng;
        $this->js_binding = $js_binding;
        $this->refinery = $refinery;
        $this->image_path_resolver = $image_path_resolver;
    }

    /**
     * @inheritdocs
     */
    public function getRendererInContext(Component $component, array $contexts) : ComponentRenderer
    {
        $name = $this->getRendererNameFor($component);
        return new $name(
            $this->ui_factory,
            $this->tpl_factory,
            $this->lng,
            $this->js_binding,
            $this->refinery,
            $this->image_path_resolver
        );
    }

    /**
     * Get the name for the renderer of Component class.
     */
    protected function getRendererNameFor(Component $component) : string
    {
        $class = get_class($component);
        $parts = explode("\\", $class);
        $parts[count($parts) - 1] = "Renderer";
        return implode("\\", $parts);
    }

    /**
     * @inheritdocs
     */
    public function getJSBinding() : JavaScriptBinding
    {
        return $this->js_binding;
    }
}
