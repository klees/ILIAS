<?php

use PHPUnit\Framework\TestCase;

/**
 * @author Alexander Killing <killing@leifos.de>
 */
class LinkStandardGUIRequestTest extends TestCase
{
    //protected $backupGlobals = false;

    protected function tearDown() : void
    {
    }

    protected function getRequest(array $get, array $post) : \ILIAS\Link\StandardGUIRequest
    {
        $http_mock = $this->createMock(ILIAS\HTTP\Services::class);
        $lng_mock = $this->createMock(ilLanguage::class);
        $data = new \ILIAS\Data\Factory();
        $refinery = new \ILIAS\Refinery\Factory($data, $lng_mock);
        return new \ILIAS\Link\StandardGUIRequest(
            $http_mock,
            $refinery,
            $get,
            $post
        );
    }

    public function testSelectedId()
    {
        $request = $this->getRequest(
            [
                "sel_id" => "123"
            ],
            [
            ]
        );

        $this->assertEquals(
            123,
            $request->getSelectedId()
        );
    }

    public function testDo()
    {
        $request = $this->getRequest(
            [
                "do" => "set"
            ],
            [
            ]
        );

        $this->assertEquals(
            "set",
            $request->getDo()
        );
    }

    public function testMediaPoolFolder()
    {
        $request = $this->getRequest(
            [
                "mep_fold" => "14"
            ],
            [
            ]
        );

        $this->assertEquals(
            14,
            $request->getMediaPoolFolder()
        );
    }

    public function testLinkType()
    {
        $request = $this->getRequest(
            [
                "link_type" => "mytype"
            ],
            [
            ]
        );

        $this->assertEquals(
            "mytype",
            $request->getLinkType()
        );
    }

    public function testLinkParentObjId()
    {
        $request = $this->getRequest(
            [
                "link_par_obj_id" => "13"
            ],
            [
            ]
        );

        $this->assertEquals(
            13,
            $request->getLinkParentObjId()
        );
    }

    public function testLinkParentFolderId()
    {
        $request = $this->getRequest(
            [
                "link_par_fold_id" => "18"
            ],
            [
            ]
        );

        $this->assertEquals(
            18,
            $request->getLinkParentFolderId()
        );
    }

    public function testLinkParentRefId()
    {
        $request = $this->getRequest(
            [
                "link_par_ref_id" => "22"
            ],
            [
            ]
        );

        $this->assertEquals(
            22,
            $request->getLinkParentRefId()
        );
    }

    public function testUserSearchString()
    {
        $request = $this->getRequest(
            [
            ],
            [
                "usr_search_str" => "term"
            ]
        );

        $this->assertEquals(
            "term",
            $request->getUserSearchStr()
        );
    }
}
