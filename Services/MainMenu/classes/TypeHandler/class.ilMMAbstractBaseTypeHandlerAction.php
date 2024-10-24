<?php

use ILIAS\GlobalScreen\Scope\MainMenu\Collector\Handler\TypeHandler;
use ILIAS\GlobalScreen\Scope\MainMenu\Collector\Renderer\BaseTypeRenderer;
use ILIAS\GlobalScreen\Scope\MainMenu\Factory\isItem;
use ILIAS\DI\Container;

/******************************************************************************
 *
 * This file is part of ILIAS, a powerful learning management system.
 *
 * ILIAS is licensed with the GPL-3.0, you should have received a copy
 * of said license along with the source code.
 *
 * If this is not the case or you just want to try ILIAS, you'll find
 * us at:
 *      https://www.ilias.de
 *      https://github.com/ILIAS-eLearning
 *
 *****************************************************************************/
/**
 * Class ilMMAbstractBaseTypeHandlerAction
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
abstract class ilMMAbstractBaseTypeHandlerAction implements TypeHandler
{

    protected array $links = [];

    const F_ACTION = 'action';
    const F_EXTERNAL = 'external';

    public function __construct()
    {
        $this->links = ilMMTypeActionStorage::getArray('identification', [self::F_ACTION, self::F_EXTERNAL]);
    }

    abstract public function matchesForType() : string;

    /**
     * @inheritdoc
     */
    abstract public function enrichItem(isItem $item) : isItem;

    /**
     * @inheritdoc
     */
    public function saveFormFields(\ILIAS\GlobalScreen\Identification\IdentificationInterface $identification, array $data) : bool
    {
        ilMMTypeActionStorage::find($identification->serialize())->setAction((string) $data[self::F_ACTION])->setExternal((bool) $data[self::F_EXTERNAL])->update();

        return true;
    }

    /**
     * @inheritdoc
     */
    public function getAdditionalFieldsForSubForm(\ILIAS\GlobalScreen\Identification\IdentificationInterface $identification) : array
    {
        global $DIC;
        /**
         * @var $DIC Container
         */

        $url = $DIC->ui()->factory()->input()->field()->text($this->getFieldTranslation())
                   ->withAdditionalTransformation($DIC->refinery()->custom()->transformation(BaseTypeRenderer::getURIConverter()))
                   ->withAdditionalTransformation($DIC->refinery()->custom()->constraint(BaseTypeRenderer::getURIChecker(), $DIC->language()->txt('err_uri_not_valid')))
                   ->withRequired(true)
                   ->withByline($this->getFieldInfoTranslation());
        if (isset($this->links[$identification->serialize()][self::F_ACTION])) {
            $url = $url->withValue($this->links[$identification->serialize()][self::F_ACTION]);
        }
        $external = $DIC->ui()->factory()->input()->field()->checkbox($DIC->language()->txt('field_external'), $DIC->language()->txt('field_external_info'));
        if (isset($this->links[$identification->serialize()][self::F_EXTERNAL])) {
            $external = $external->withValue((bool) $this->links[$identification->serialize()][self::F_EXTERNAL]);
        }

        return [self::F_ACTION => $url, self::F_EXTERNAL => $external];
    }

    /**
     * @return string
     */
    abstract protected function getFieldTranslation() : string;

    abstract protected function getFieldInfoTranslation() : string;
}
