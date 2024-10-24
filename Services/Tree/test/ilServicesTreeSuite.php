<?php declare(strict_types=1);

/* Copyright (c) 1998-2009 ILIAS open source, Extended GPL, see docs/LICENSE */

use PHPUnit\Framework\TestSuite;

class ilServicesTreeSuite extends TestSuite
{
    public static function suite()
    {
        $suite = new ilServicesTreeSuite();

        include_once("./Services/Tree/test/ilTreeTest.php");
        $suite->addTestSuite("ilTreeTest");

        include_once './Services/Tree/test/ilRepositoryTreeTest.php';
        $suite->addTestSuite(ilRepositoryTreeTest::class);

        return $suite;
    }
}
