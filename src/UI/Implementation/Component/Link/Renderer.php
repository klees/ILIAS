<?php declare(strict_types=1);

/* Copyright (c) 2017 Alexander Killing <killing@leifos.de> Extended GPL, see docs/LICENSE */

namespace ILIAS\UI\Implementation\Component\Link;

use ILIAS\UI\Implementation\Render\AbstractComponentRenderer;
use ILIAS\UI\Implementation\Render\Template;
use ILIAS\UI\Renderer as RendererInterface;
use ILIAS\UI\Component;
use LogicException;

class Renderer extends AbstractComponentRenderer
{
    /**
     * @inheritdoc
     */
    public function render(Component\Component $component, RendererInterface $default_renderer) : string
    {
        $this->checkComponent($component);

        if ($component instanceof Component\Link\Standard) {
            return $this->renderStandard($component);
        }
        if ($component instanceof Component\Link\Bulky) {
            return $this->renderBulky($component, $default_renderer);
        }
        throw new LogicException("Cannot render: " . get_class($component));
    }

    protected function setStandardVars(
        string $tpl_name,
        Component\Link\Link $component
    ) : Template {
        $tpl = $this->getTemplate($tpl_name, true, true);
        $action = $component->getAction();
        $label = $component->getLabel();
        if ($component->getOpenInNewViewport()) {
            $tpl->touchBlock("open_in_new_viewport");
        }
        $tpl->setVariable("LABEL", $label);
        $tpl->setVariable("HREF", $action);
        return $tpl;
    }

    protected function renderStandard(
        Component\Link\Standard $component
    ) : string {
        $tpl_name = "tpl.standard.html";
        $tpl = $this->setStandardVars($tpl_name, $component);
        return $tpl->get();
    }

    protected function renderBulky(
        Component\Link\Bulky $component,
        RendererInterface $default_renderer
    ) : string {
        $tpl_name = "tpl.bulky.html";
        $tpl = $this->setStandardVars($tpl_name, $component);
        $renderer = $default_renderer->withAdditionalContext($component);
        $tpl->setVariable("SYMBOL", $renderer->render($component->getSymbol()));

        $id = $this->bindJavaScript($component);
        $tpl->setVariable("ID", $id);

        $aria_role = $component->getAriaRole();
        if ($aria_role != null) {
            $tpl->setCurrentBlock("with_aria_role");
            $tpl->setVariable("ARIA_ROLE", $aria_role);
            $tpl->parseCurrentBlock();
        }

        return $tpl->get();
    }

    /**
     * @inheritdoc
     */
    protected function getComponentInterfaceName() : array
    {
        return [
            Component\Link\Standard::class,
            Component\Link\Bulky::class
        ];
    }
}
