<?php

/* Copyright (c) 2020 Richard Klees <richard.klees@concepts-and-training.de> Extended GPL, see docs/LICENSE */

/**
 * This standalone script helps ImplementationOfInterfaceFinder to find classes
 * that implement a certain interface. When using \ReflectionClass in the original
 * process we will use a lot of memory since PHP never frees class information it
 * once loaded. So this helps to save some memory.
 *
 * See https://mantis.ilias.de/view.php?id=28848
 */

require_once(__DIR__ . "/../../libs/composer/vendor/autoload.php");

if (!php_sapi_name() === "cli") {
    die();
}

$class_name = $_SERVER["argv"][1];
$interface_name = $_SERVER["argv"][2];

$r = new \ReflectionClass($class_name);
if ($r->isInstantiable() && $r->implementsInterface($interface_name)) {
    echo "TRUE";
} else {
    echo "FALSE";
}
