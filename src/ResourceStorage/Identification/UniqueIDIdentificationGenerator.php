<?php declare(strict_types=1);

namespace ILIAS\ResourceStorage\Identification;

use ILIAS\Data\UUID\Factory;

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
 * Class UniqueIDIdentificationGenerator
 * @author Fabian Schmid <fs@studer-raimann.ch>
 * @internal
 */
class UniqueIDIdentificationGenerator implements IdentificationGenerator
{
    protected \ILIAS\Data\UUID\Factory $factory;

    /**
     * UniqueIDIdentificationGenerator constructor.
     */
    public function __construct()
    {
        $this->factory = new Factory();
    }

    /**
     * @throws \Exception
     */
    public function getUniqueResourceIdentification() : ResourceIdentification
    {
        try {
            $unique_id = $this->factory->uuid4AsString();
        } catch (\Exception $e) {
            throw new \LogicException('Generating uuid failed: ' . $e->getMessage(), $e->getCode(), $e);
        } finally {
            return new ResourceIdentification($unique_id);
        }
    }
}
