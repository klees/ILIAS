<?php

namespace ILIAS\BackgroundTasks\Types;

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
 * Class VoidType
 * @package ILIAS\Types
 * @author  Oskar Truffer <ot@studer-raimann.ch>
 * Void Type and Singleton for the void type.
 */
class VoidType implements Type
{
    protected static ?\ILIAS\BackgroundTasks\Types\VoidType $instance = null;
    
    /**
     * Just to make it protected.
     * VoidValue constructor.
     */
    protected function __construct()
    {
    }
    
    public static function instance() : ?\ILIAS\BackgroundTasks\Types\VoidType
    {
        if (self::instance() === null) {
            self::$instance = new VoidType();
        }
        
        return self::$instance;
    }
    
    /**
     * @return string A string representation of the Type.
     */
    public function __toString()
    {
        return "Void";
    }
    
    /**
     * Is this type a subtype of $type. Not strict! x->isSubtype(x) == true.
     * @param $type Type
     */
    public function isExtensionOf(Type $type) : bool
    {
        return $type instanceof VoidType;
    }
    
    /**
     * returns true if the two types are equal.
     */
    public function equals(Type $otherType) : bool
    {
        return $otherType instanceof VoidType;
    }
}
