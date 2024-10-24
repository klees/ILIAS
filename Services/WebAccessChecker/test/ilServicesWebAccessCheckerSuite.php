<?php
use PHPUnit\Framework\TestSuite;

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
 * ilServicesWebAccessCheckerSuite
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version 1.0.0
 */
class ilServicesWebAccessCheckerSuite extends TestSuite
{
    public static function suite() : \ilServicesWebAccessCheckerSuite
    {
        //require_once('./Services/WebAccessChecker/test/Token/ilWACTokenTest.php');
        //$suite->addTestSuite('ilWACTokenTest');

        //require_once('./Services/WebAccessChecker/test/CheckingInstance/ilWACCheckingInstanceTest.php');
        //$suite->addTestSuite('ilWACCheckingInstanceTest');

        //require_once('./Services/WebAccessChecker/test/Path/ilWACPathTest.php');
        //$suite->addTestSuite('ilWACPathTest');

        return new self();
    }
}
