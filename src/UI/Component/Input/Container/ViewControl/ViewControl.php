<?php declare(strict_types=1);

/* Copyright (c) 2020 Nils Haagen <nils.haagen@concepts-and-training.de> Extended GPL, see docs/LICENSE */

namespace ILIAS\UI\Component\Input\Container\ViewControl;

use ILIAS\UI\Component\Component;
use Psr\Http\Message\ServerRequestInterface;
use ILIAS\UI\Component\Input\Field\Input;

/**
 * This describes a View Control Container.
 */
interface ViewControl extends Component
{
    /**
     * Get the contained controls.
     *
     * @return array<string,Input>
     */
    public function getInputs() : array;

    public function withRequest(ServerRequestInterface $request);

    /**
     * @return array<string,mixed>
     */
    public function getData() : array;
}
