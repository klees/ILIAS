<?php

namespace ILIAS\BackgroundTasks\Dependencies;

use ILIAS\BackgroundTasks\Dependencies\DependencyMap\DependencyMap;
use ILIAS\BackgroundTasks\Dependencies\Exceptions\InvalidClassException;
use ILIAS\DI\Container;
use ReflectionParameter;

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
 * Class Factory
 * @package ILIAS\BackgroundTasks\Dependencies
 * Create instances of classes using type hinting and the dependency injection container.
 * @author  Oskar Truffer <ot@studer-raimann.ch>
 */
class Injector
{
    protected \ILIAS\DI\Container $dic;
    protected \ILIAS\BackgroundTasks\Dependencies\DependencyMap\DependencyMap $dependencyMap;
    
    /**
     * Factory constructor.
     * @param               $dic Container
     */
    public function __construct(Container $dic, DependencyMap $dependencyMap)
    {
        $this->dic = $dic;
        $this->dependencyMap = $dependencyMap;
    }
    
    /**
     * @param       $fullyQualifiedClassName string The given class must type hint all its
     *                                       constructor arguments. Furthermore the types must
     *                                       exist in the DI-Container.
     */
    public function createInstance($fullyQualifiedClassName, bool $requireFile = false, callable $with = null) : object
    {
        // The reflection classes needed.
        $reflectionClass = new \ReflectionClass($fullyQualifiedClassName);
        $constructor = $reflectionClass->getConstructor();
        if ($constructor === null) {
            return $reflectionClass->newInstance();
        }
        
        $parameters = $constructor->getParameters();
        
        // we get the arguments to construct the object from the DIC and Typehinting.
        $constructorArguments = $this->createConstructorArguments($fullyQualifiedClassName, $parameters, $with);
        
        // Crate the instance with the arguments.
        return $reflectionClass->newInstanceArgs($constructorArguments);
    }
    
    /**
     * @param       $fullyQualifiedClassName string
     * @param       $parameters              ReflectionParameter[]
     * @return mixed[]
     */
    protected function createConstructorArguments($fullyQualifiedClassName, $parameters, $with) : array
    {
        $constructorArguments = [];
        
        foreach ($parameters as $parameter) {
            // As long as there are given arguments we take those.
            $constructorArguments[] = $this->getDependency($fullyQualifiedClassName, $parameter, $with);
        }
        
        return $constructorArguments;
    }
    
    /**
     * @param          $fullyQualifiedClassName  string
     * @param          $parameter                ReflectionParameter
     * @return mixed|void
     * @throws InvalidClassException
     */
    protected function getDependency($fullyQualifiedClassName, $parameter, callable $with = null)
    {
        // These Lines are currently commented while we cant use $parameter->getType() which will be part of PHP7
        //		if (!$parameter->getType()) {
        //			throw new InvalidClassException("The constructor of $fullyQualifiedClassName is not fully type hinted, or the type hints cannot be resolved.");
        //		}
        
        //		$type = $parameter->getType()->__toString();
        $type = $parameter->getClass()->getName();
        
        //		if ($parameter->getType()->isBuiltin()) {
        //			throw new InvalidClassException("The DI cannot instantiate $fullyQualifiedClassName because some of the constructors arguments are built in types. Only interfaces (and objects) are stored in the DI-Container.");
        //		}
        
        if (!$type) {
            throw new InvalidClassException("The DI cannot instantiate $fullyQualifiedClassName because some of the constructors arguments are not type hinted. Make sure all parameters in the constructor have type hinting.");
        }
        
        if ($with) {
            return $this->dependencyMap->getDependencyWith($this->dic, $type, $fullyQualifiedClassName, $with);
        } else {
            return $this->dependencyMap->getDependency($this->dic, $type, $fullyQualifiedClassName);
        }
    }
}
