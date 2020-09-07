<?php

namespace ILIAS\Setup;

/**
 * Class ImplementationOfInterfaceFinder
 *
 * @package ILIAS\ArtifactBuilder\Generators
 */
class ImplementationOfInterfaceFinder
{
    /**
     * @var string
     */
    private $php_binary;

    /**
     * @var string
     */
    private $interface = "";
    /**
     * @var array
     */
    private $ignore
        = [
            '.*/libs/',
            '.*/test/',
            '.*/tests/',
            '.*/setup/',
            // Classes using removed Auth-class from PEAR
            '.*ilSOAPAuth.*',
            // Classes using unknown
            '.*ilPDExternalFeedBlockGUI.*',
        ];


    public function __construct(string $php_binary, string $interface)
    {
        $this->php_binary = $php_binary;
        $this->interface = $interface;
        $this->getAllClassNames();
    }


    protected function getAllClassNames() : \Iterator
    {
        // We use the composer classmap ATM
        $composer_classmap = include "./libs/composer/vendor/composer/autoload_classmap.php";
        $root = substr(__FILE__, 0, strpos(__FILE__, "/src"));

        if (!is_array($composer_classmap)) {
            throw new \LogicException("Composer ClassMap not loaded");
        }

        $regexp = implode(
            "|",
            array_map(
                // fix path-separators to respect windows' backspaces.
                function ($v) {
                    return "(" . str_replace('/', '(/|\\\\)', $v) . ")";
                },
                $this->ignore
            )
        );

        foreach ($composer_classmap as $class_name => $file_path) {
            $path = str_replace($root, "", realpath($file_path));
            if (!preg_match("#^" . $regexp . "$#", $path)) {
                yield $class_name;
            }
        }
    }


    public function getMatchingClassNames() : \Iterator
    {
        $script = escapeshellarg(__DIR__ . "/has_interface.php");

        foreach ($this->getAllClassNames() as $class_name) {
            $status = 0;
            $output = [];
            $arg1 = escapeshellarg($class_name);
            $arg2 = escapeshellarg($this->interface);
            exec($this->php_binary . " $script $arg1 $arg2", $output, $status);

            if ($status === 0 && $output[0] === "TRUE") {
                yield $class_name;
            }
        }
    }
}
