<?php declare(strict_types=1);

/* Copyright (c) 1998-2020 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * Class ilTestPassDetailsOverviewTableGUITest
 * @author Marvin Beym <mbeym@databay.de>
 */
class ilTestPassDetailsOverviewTableGUITest extends ilTestBaseTestCase
{
    private ilTestPassDetailsOverviewTableGUI $tableGui;
    private ilObjTestGUI $parentObj_mock;

    protected function setUp() : void
    {
        parent::setUp();

        $lng_mock = $this->createMock(ilLanguage::class);
        $ctrl_mock = $this->createMock(ilCtrl::class);
        $ctrl_mock->expects($this->any())
                  ->method("getFormAction")
                  ->willReturnCallback(function () {
                      return "testFormAction";
                  });

        $this->setGlobalVariable("lng", $lng_mock);
        $this->setGlobalVariable("ilCtrl", $ctrl_mock);
        $this->setGlobalVariable("tpl", $this->createMock(ilGlobalPageTemplate::class));
        $this->setGlobalVariable("component.repository", $this->createMock(ilComponentRepository::class));
        $component_factory = $this->createMock(ilComponentFactory::class);
        $component_factory->method("getActivePluginsInSlot")->willReturn(new ArrayIterator());
        $this->setGlobalVariable("component.factory", $component_factory);
        $this->setGlobalVariable("ilPluginAdmin", new ilPluginAdmin($this->createMock(ilComponentRepository::class)));
        $this->setGlobalVariable("ilDB", $this->createMock(ilDBInterface::class));

        $this->parentObj_mock = $this->createMock(ilObjTestGUI::class);
        $this->parentObj_mock->object = $this->createMock(ilObjTest::class);
        $this->tableGui = new ilTestPassDetailsOverviewTableGUI(
            $ctrl_mock,
            $this->parentObj_mock,
            ""
        );
    }

    public function test_instantiateObject_shouldReturnInstance() : void
    {
        $this->assertInstanceOf(ilTestPassDetailsOverviewTableGUI::class, $this->tableGui);
    }

    public function testPdfGenerationRequest() : void
    {
        $this->assertIsBool($this->tableGui->isPdfGenerationRequest());
        $this->tableGui->setIsPdfGenerationRequest(false);
        $this->assertFalse($this->tableGui->isPdfGenerationRequest());
        $this->tableGui->setIsPdfGenerationRequest(true);
        $this->assertTrue($this->tableGui->isPdfGenerationRequest());
    }

    public function testSingleAnswerScreenCmd() : void
    {
        $this->tableGui->setSingleAnswerScreenCmd("testString");
        $this->assertEquals("testString", $this->tableGui->getSingleAnswerScreenCmd());
    }

    public function testAnswerListAnchorEnabled() : void
    {
        $this->assertIsBool($this->tableGui->getAnswerListAnchorEnabled());
        $this->tableGui->setAnswerListAnchorEnabled(false);
        $this->assertFalse($this->tableGui->getAnswerListAnchorEnabled());
        $this->tableGui->setAnswerListAnchorEnabled(true);
        $this->assertTrue($this->tableGui->getAnswerListAnchorEnabled());
    }

    public function testShowHintCount() : void
    {
        $this->assertIsBool($this->tableGui->getShowHintCount());
        $this->tableGui->setShowHintCount(false);
        $this->assertFalse($this->tableGui->getShowHintCount());
        $this->tableGui->setShowHintCount(true);
        $this->assertTrue($this->tableGui->getShowHintCount());
    }

    public function testShowSuggestedSolution() : void
    {
        $this->assertIsBool($this->tableGui->getShowSuggestedSolution());
        $this->tableGui->setShowSuggestedSolution(false);
        $this->assertFalse($this->tableGui->getShowSuggestedSolution());
        $this->tableGui->setShowSuggestedSolution(true);
        $this->assertTrue($this->tableGui->getShowSuggestedSolution());
    }

    public function testActiveId() : void
    {
        $this->tableGui->setActiveId(200);
        $this->assertEquals(200, $this->tableGui->getActiveId());
    }

    public function testObjectiveOrientedPresentationEnabled() : void
    {
        $this->assertIsBool($this->tableGui->isObjectiveOrientedPresentationEnabled());
        $this->tableGui->setObjectiveOrientedPresentationEnabled(false);
        $this->assertFalse($this->tableGui->isObjectiveOrientedPresentationEnabled());
        $this->tableGui->setObjectiveOrientedPresentationEnabled(true);
        $this->assertTrue($this->tableGui->isObjectiveOrientedPresentationEnabled());
    }

    public function testMultipleObjectivesInvolved() : void
    {
        $this->assertIsBool($this->tableGui->areMultipleObjectivesInvolved());
        $this->tableGui->setMultipleObjectivesInvolved(false);
        $this->assertFalse($this->tableGui->areMultipleObjectivesInvolved());
        $this->tableGui->setMultipleObjectivesInvolved(true);
        $this->assertTrue($this->tableGui->areMultipleObjectivesInvolved());
    }

    public function testPassColumnEnabled() : void
    {
        $this->assertIsBool($this->tableGui->isPassColumnEnabled());
        $this->tableGui->setPassColumnEnabled(false);
        $this->assertFalse($this->tableGui->isPassColumnEnabled());
        $this->tableGui->setPassColumnEnabled(true);
        $this->assertTrue($this->tableGui->isPassColumnEnabled());
    }
}
