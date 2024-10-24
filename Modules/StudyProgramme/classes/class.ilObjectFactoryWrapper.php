<?php declare(strict_types=1);

/* Copyright (c) 2015 Richard Klees <richard.klees@concepts-and-training.de> Extended GPL, see docs/LICENSE */

require_once("./Services/Object/classes/class.ilObjectFactory.php");

/**
 * Class ilObjectFactoryWrapper.
 *
 * Wraps around static class ilObjectFactory to make the object factory
 * exchangeable in ilObjStudyProgramm for testing purpose.
 *
 * @author : Richard Klees <richard.klees@concepts-and-training.de>
 */
class ilObjectFactoryWrapper
{
    public static ?ilObjectFactoryWrapper $instance = null;
    
    public static function singleton() : ilObjectFactoryWrapper
    {
        if (self::$instance === null) {
            self::$instance = new ilObjectFactoryWrapper();
        }
        return self::$instance;
    }

    /**
     * @return bool|ilObject
     */
    public function getInstanceByRefId(int $ref_id, bool $stop_on_error = true)
    {
        return ilObjectFactory::getInstanceByRefId($ref_id, $stop_on_error);
    }
}
