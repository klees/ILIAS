<?php declare(strict_types=1);

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

use PHPUnit\Framework\TestSuite;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/bootstrap.php';

/**
 * Class ilServicesCronSuite
 * @author Michael Jansen <mjansen@databay.de>
 */
class ilServicesCronSuite extends TestSuite
{
    /**
     * @return self
     * @throws ReflectionException
     */
    public static function suite() : self
    {
        $suite = new self();

        require_once __DIR__ . '/CronJobEntityTest.php';
        $suite->addTestSuite(CronJobEntityTest::class);

        require_once __DIR__ . '/CronJobScheduleTest.php';
        $suite->addTestSuite(CronJobScheduleTest::class);

        require_once __DIR__ . '/CronJobManagerTest.php';
        $suite->addTestSuite(CronJobManagerTest::class);

        return $suite;
    }
}
