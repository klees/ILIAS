<?php declare(strict_types=1);
/* Copyright (c) 1998-2018 ILIAS open source, Extended GPL, see docs/LICENSE */

use PHPUnit\Framework\TestSuite;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/bootstrap.php';

/**
 * Class ilServicesTermsOfServiceSuite
 * @author Michael Jansen <mjansen@databay.de>
 */
class ilServicesTermsOfServiceSuite extends TestSuite
{
    public static function suite() : self
    {
        $suite = new self();

        foreach (new RegExIterator(
            new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator(__DIR__, FilesystemIterator::SKIP_DOTS),
                RecursiveIteratorIterator::LEAVES_ONLY
            ),
            '/BaseTest\.php$/'
        ) as $file) {
            /** @var SplFileInfo $file */
            require_once $file->getPathname();
        }

        foreach (new RegExIterator(
            new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator(__DIR__, FilesystemIterator::SKIP_DOTS),
                RecursiveIteratorIterator::LEAVES_ONLY
            ),
            '/(?<!Base)Test\.php$/'
        ) as $file) {
            /** @var SplFileInfo $file */
            require_once $file->getPathname();

            $className = preg_replace('/(.*?)(\.php)/', '$1', $file->getBasename());
            if (class_exists($className)) {
                $reflection = new ReflectionClass($className);
                if (
                    !$reflection->isAbstract() &&
                    !$reflection->isInterface() &&
                    $reflection->isSubclassOf(TestCase::class)) {
                    $suite->addTestSuite($className);
                }
            }
        }

        return $suite;
    }
}
