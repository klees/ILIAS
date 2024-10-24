<?php

/**
 * This file is part of ILIAS, a powerful learning management system
 * published by ILIAS open source e-Learning e.V.
 * ILIAS is licensed with the GPL-3.0,
 * see https://www.gnu.org/licenses/gpl-3.0.en.html
 * You should have received a copy of said license along with the
 * source code, too.
 * If this is not the case or you just want to try ILIAS, you'll find
 * us at:
 * https://www.ilias.de
 * https://github.com/ILIAS-eLearning
 */

namespace ILIAS\Style\Content;

use ILIAS\Style\Content\Access\StyleAccessManager;

/**
 * @author Alexander Killing <killing@leifos.de>
 */
class ImageUIFactory
{
    protected InternalGUIService $gui_service;
    protected InternalDomainService $domain_service;

    public function __construct(
        InternalDomainService $domain_service,
        InternalGUIService $gui_service
    ) {
        $this->domain_service = $domain_service;
        $this->gui_service = $gui_service;
    }
    // images editing
    public function ilContentStyleImageGUI(
        StyleAccessManager $access_manager,
        ImageManager $image_manager
    ) : \ilContentStyleImageGUI {
        return new \ilContentStyleImageGUI(
            $this->domain_service,
            $this->gui_service,
            $access_manager,
            $image_manager
        );
    }
}
