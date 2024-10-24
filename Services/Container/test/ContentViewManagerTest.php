<?php

use PHPUnit\Framework\TestCase;

/**
 * Test clipboard repository
 *
 * @author Alexander Killing <killing@leifos.de>
 */
class ContentViewManagerTest extends TestCase
{
    //protected $backupGlobals = false;
    protected \ILIAS\Container\Content\ViewManager $manager;

    protected function setUp() : void
    {
        parent::setUp();
        $view_repo = new \ILIAS\Container\Content\ViewSessionRepository();
        $this->manager = new \ILIAS\Container\Content\ViewManager($view_repo);
    }

    protected function tearDown() : void
    {
    }

    /**
     * Test admin view
     */
    public function testAdminView()
    {
        $manager = $this->manager;

        $manager->setAdminView();

        $this->assertEquals(
            true,
            $manager->isAdminView()
        );
        $this->assertEquals(
            false,
            $manager->isContentView()
        );
    }

    /**
     * Test content view
     */
    public function testContentView()
    {
        $manager = $this->manager;

        $manager->setContentView();

        $this->assertEquals(
            false,
            $manager->isAdminView()
        );
        $this->assertEquals(
            true,
            $manager->isContentView()
        );
    }
}
