<?php

require_once("libs/composer/vendor/autoload.php");

/**
 *	Defines tests every UI-factory should pass.
 *	
 */
abstract class FactoryAbstractTest extends PHPUnit_Framework_TestCase {
    abstract public function getFactoryInstance();

    /**
     *	This will be used in tests of the UI component creation of a factory.
     *	Make sure all the UI Components, which your factory may create,
     *	are listed here by the format array("methodName" => mixed[] $args).
     *	If a method takes no parameters, use "methodName" => array().
     *	If you skip any methods, assertions will fail...
     *
     *	@return	mixed["methodName"][]
	 */
    abstract protected function UIComponentCreationArguments();

	public function test_any_interfaces_are_factories() {
		if(count($this->factoryInterfaceReflections($this->getFactoryInstance())) > 0) {
			$this->assertTrue(true);
		} else {
			$this->assertFalse('no factory interfaces found');
		}
	}

	public function test_ui_component_creation() {
		$f = $this->getFactoryInstance();
		$ui_component_creation_arguments = $this->UIComponentCreationArguments();

		$method_reflections = array();

		foreach ($this->getFactoryInterfaceReflections($f) as $interface_name => $interface_reflection) {
			$method_reflections = array_merge($method_reflections, $interface_reflection->getMethods());
		}

		foreach ($method_reflections as $method_reflection) {
			if(isset($ui_component_creation_arguments[$method_reflection->getName()])) {
				$possible_ui_component = $method_reflection->invokeArgs($f,$ui_component_creation_arguments[$method_reflection->getName()]);
				$this->assertTrue($possible_ui_component instanceof \ILIAS\UI\Cmponent);
			}
		}
	}

	protected function test_factory_creation() {
		$f = $this->getFactoryInstance();
		$ui_component_method_namess = array_keys($this->UIComponentCreationArguments());

		$method_reflections = array();

		foreach ($this->getFactoryInterfaceReflections($f) as $interface_name => $interface_reflection) {
			$method_reflections = array_merge($method_reflections, $interface_reflection->getMethods());
		}

		foreach ($method_reflections as $method_reflection) {
			if(in_array($method_reflection->getName(),$ui_component_method_names)) {
				$this->assertTrue($method_reflection->getNumberOfParameters() === 0);
				$possible_factory = $method_reflection->invokeArgs($f);
				$this->assertTrue(count($this->factoryInterfaceReflections($possible_factory)) > 0);
			}
		}
	}

	protected function factoryInterfaceReflections($factory) {
		return array_filter($this->interfaceReflections($factory),function ($factory) {return $this->interfaceIsFactory($factory);});
	}

	protected function interfaceReflections(\ILIAS\UI\Factory $factory) {
		return (new ReflectionClass($factory))->getInterfaces();
	}

	protected function interfaceIsFactory(ReflectionClass $reflection) {
		return $reflection->getNamespace() === "ILIAS\\UI\\Factory";
	}
}