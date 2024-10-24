<?php declare(strict_types=1);

/* Copyright (c) 1998-2020 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * Class ilTestRandomQuestionSetConfigStateMessageHandlerTest
 * @author Marvin Beym <mbeym@databay.de>
 */
class ilTestRandomQuestionSetConfigStateMessageHandlerTest extends ilTestBaseTestCase
{
    private ilTestRandomQuestionSetConfigStateMessageHandler $testObj;

    protected function setUp() : void
    {
        parent::setUp();

        $this->testObj = new ilTestRandomQuestionSetConfigStateMessageHandler(
            $this->createMock(ilLanguage::class),
            $this->createMock(ilCtrl::class)
        );
    }

    public function test_instantiateObject_shouldReturnInstance() : void
    {
        $this->assertInstanceOf(ilTestRandomQuestionSetConfigStateMessageHandler::class, $this->testObj);
    }

    public function testLostPools() : void
    {
        $expected = [
            new ilTestRandomQuestionSetNonAvailablePool(),
            new ilTestRandomQuestionSetNonAvailablePool(),
            new ilTestRandomQuestionSetNonAvailablePool()
        ];

        $this->testObj->setLostPools($expected);
        $this->assertEquals($expected, $this->testObj->getLostPools());
    }

    public function testParticipantDataExists() : void
    {
        $this->testObj->setParticipantDataExists(false);
        $this->assertFalse($this->testObj->doesParticipantDataExists());

        $this->testObj->setParticipantDataExists(true);
        $this->assertTrue($this->testObj->doesParticipantDataExists());
    }

    public function testTargetGUI() : void
    {
        $targetGui_mock = $this->createMock(ilTestRandomQuestionSetConfigGUI::class);
        $this->testObj->setTargetGUI($targetGui_mock);
        $this->assertEquals($targetGui_mock, $this->testObj->getTargetGUI());
    }

    public function testContext() : void
    {
        $this->testObj->setContext("test");
        $this->assertEquals("test", $this->testObj->getContext());
    }

    public function testQuestionSetConfig() : void
    {
        $mock = $this->createMock(ilTestRandomQuestionSetConfig::class);
        $this->testObj->setQuestionSetConfig($mock);
        $this->assertEquals($mock, $this->testObj->getQuestionSetConfig());
    }

    public function testValidationFailed() : void
    {
        $this->testObj->setValidationFailed(false);
        $this->assertFalse($this->testObj->isValidationFailed());

        $this->testObj->setValidationFailed(true);
        $this->assertTrue($this->testObj->isValidationFailed());
    }

    public function testHasValidationReport() : void
    {
        $expected = [
            "test1",
            "test2"
        ];
        $this->testObj->addValidationReport($expected[0]);
        $this->testObj->addValidationReport($expected[1]);
        $this->assertEquals(2, $this->testObj->hasValidationReports());
    }

    public function testGetValidationReportHtml() : void
    {
        $expected = "test1<br />test2";
        $this->testObj->addValidationReport("test1");
        $this->testObj->addValidationReport("test2");
        $this->assertEquals($expected, $this->testObj->getValidationReportHtml());
    }
}
