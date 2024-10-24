<?php declare(strict_types=1);

/* Copyright (c) 2019 Nils Haagen <nils.haagen@concepts-and-training.de> Extended GPL, see docs/LICENSE */

namespace ILIAS\UI\Implementation\Component\Tree;

use ILIAS\UI\Implementation\Render\AbstractComponentRenderer;
use ILIAS\UI\Renderer as RendererInterface;
use ILIAS\UI\Component;
use ILIAS\UI\Component\Tree;
use ILIAS\UI\Implementation\Render\ResourceRegistry;

class Renderer extends AbstractComponentRenderer
{
    /**
     * @inheritdoc
     */
    public function render(Component\Component $component, RendererInterface $default_renderer) : string
    {
        $this->checkComponent($component);

        /**
         * @var $component Tree\Expandable
         */

        $tpl_name = "tpl.tree.html";
        $tpl = $this->getTemplate($tpl_name, true, true);

        $tpl->setVariable("ARIA_LABEL", $component->getLabel());

        $nodes = [];
        foreach ($component->getData() as $record) {
            $nodes[] = $this->buildNode(
                $component->getRecursion(),
                $record,
                $component->getEnvironment()
            );
        }

        $nodes_html = $default_renderer->render($nodes);

        if ($component->isSubTree()) {
            return $nodes_html;
        }

        $tpl->setVariable('NODES', $nodes_html);

        $highlight_node_on_click = $component->getHighlightOnNodeClick();
        $component = $component->withAdditionalOnLoadCode(
            function ($id) use ($highlight_node_on_click) {
                return "il.UI.tree.init('$id', $highlight_node_on_click)";
            }
        );

        $id = $this->bindJavaScript($component);
        $tpl->setVariable("ID", $id);


        return $tpl->get();
    }

    /**
     * Trigger TreeRecursion::build and recurse into hierarchy by checking for
     * further children of the record.
     */
    protected function buildNode(
        Tree\TreeRecursion $recursion,
        $record,
        $environment
    ) : Tree\Node\Node {
        $node = $recursion->build(
            $this->getUIFactory()->tree()->node(),
            $record,
            $environment
        );

        foreach ($recursion->getChildren($record, $environment) as $sub_record) {
            $node = $node->withAdditionalSubnode(
                $this->buildNode($recursion, $sub_record, $environment)
            );
        }

        return $node;
    }

    /**
     * @inheritdoc
     */
    public function registerResources(ResourceRegistry $registry) : void
    {
        parent::registerResources($registry);
        $registry->register('./src/UI/templates/js/Tree/tree.js');
    }

    /**
     * @inheritdoc
     */
    protected function getComponentInterfaceName() : array
    {
        return array(
            Tree\Expandable::class
        );
    }
}
