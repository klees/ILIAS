<?php declare(strict_types=1);

/* Copyright (c) 2017 Richard Klees <richard.klees@concepts-and-training.de> Extended GPL, see docs/LICENSE */

namespace ILIAS\UI\Implementation\Render;

use ILIAS\UI\Component\Component;

/**
 * This is the interface that components should use if they want to load specific
 * renderers.
 */
interface RendererFactory
{
    /**
     * Get a renderer based on the current context.
     *
     * Context names are fully qualified component names.
     *
     * @param string[] $contexts
     */
    public function getRendererInContext(Component $component, array $contexts) : ComponentRenderer;

    // TODO: This is missing some method to enumerate contexts and the different
    // renderers. This would be needed to show different renderings in the Kitchen
    // Sink.

    /**
     * Todo: This was implemented to fix 21830. Do we really want this on the renderer
     * factory interfaces?
     */
    public function getJSBinding() : JavaScriptBinding;
}
