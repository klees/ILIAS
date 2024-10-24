<?php

use PHPUnit\Framework\TestCase;

/**
 * @author Alexander Killing <killing@leifos.de>
 */
class BookManStandardGUIRequestTest extends TestCase
{
    //protected $backupGlobals = false;

    protected function setUp() : void
    {
        parent::setUp();
    }

    protected function tearDown() : void
    {
    }

    protected function getRequest(array $get, array $post) : \ILIAS\BookingManager\StandardGUIRequest
    {
        $http_mock = $this->createMock(ILIAS\HTTP\Services::class);
        $lng_mock = $this->createMock(ilLanguage::class);
        $data = new \ILIAS\Data\Factory();
        $refinery = new \ILIAS\Refinery\Factory($data, $lng_mock);
        return new \ILIAS\BookingManager\StandardGUIRequest(
            $http_mock,
            $refinery,
            $get,
            $post
        );
    }

    public function testRefId()
    {
        $request = $this->getRequest(
            [
                "ref_id" => "5"
            ],
            []
        );

        $this->assertEquals(
            5,
            $request->getRefId()
        );
    }

    public function testPoolRefId()
    {
        $request = $this->getRequest(
            [
                "pool_ref_id" => "6"
            ],
            []
        );

        $this->assertEquals(
            6,
            $request->getPoolRefId()
        );
    }

    public function testReservationIds()
    {
        $request = $this->getRequest(
            [
            ],
            [
                "reservation_id" => ["4", "6", "8"]
            ]
        );

        $this->assertEquals(
            [4, 6, 8],
            $request->getReservationIds()
        );
    }
}
