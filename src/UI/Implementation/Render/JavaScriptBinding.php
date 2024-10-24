<?php declare(strict_types=1);

/* Copyright (c) 2016 Richard Klees <richard.klees@concepts-and-training.de> Extended GPL, see docs/LICENSE */

namespace ILIAS\UI\Implementation\Render;

/**
 * Provides methods to interface with javascript.
 */
interface JavaScriptBinding
{
    /**
     * Create a fresh unique id.
     *
     * This MUST return a new id on every call.
     */
    public function createId() : string;

    /**
     * Add some JavaScript-statements to the on-load handler of the page.
     */
    public function addOnLoadCode(string $code) : void;

    /**
     * Get all the registered on-load javascript code for the async context, e.g. return all code
     * inside <script> tags
     */
    public function getOnLoadCodeAsync() : string;
}
