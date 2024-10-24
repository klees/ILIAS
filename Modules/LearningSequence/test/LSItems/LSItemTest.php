<?php declare(strict_types=1);

/* Copyright (c) 2021 - Daniel Weise <daniel.weise@concepts-and-training.de> - Extended GPL, see LICENSE */

use PHPUnit\Framework\TestCase;

class LSItemTest extends TestCase
{
    const TYPE = "type";
    const TITLE = "tile";
    const DESC = "description";
    const ICON_PATH = "icon_path";
    const IS_ONLINE = true;
    const ORDER_NUMBER = 10;
    const REF_ID = 30;

    protected ilLSPostCondition $post_condition;

    protected function setUp() : void
    {
        $this->post_condition = new ilLSPostCondition(666, 'always');
    }

    public function testCreate() : LSItem
    {
        $object = new LSItem(
            self::TYPE,
            self::TITLE,
            self::DESC,
            self::ICON_PATH,
            self::IS_ONLINE,
            self::ORDER_NUMBER,
            $this->post_condition,
            self::REF_ID
        );

        $this->assertEquals(self::TYPE, $object->getType());
        $this->assertEquals(self::TITLE, $object->getTitle());
        $this->assertEquals(self::DESC, $object->getDescription());
        $this->assertEquals(self::ICON_PATH, $object->getIconPath());
        $this->assertEquals(self::IS_ONLINE, $object->isOnline());
        $this->assertEquals(self::ORDER_NUMBER, $object->getOrderNumber());
        $this->assertEquals($object->getPostCondition(), $this->post_condition);
        $this->assertEquals(self::REF_ID, $object->getRefId());

        return $object;
    }

    /**
     * @depends testCreate
     */
    public function testWithOnline(LSItem $object)
    {
        $new_obj = $object->withOnline(false);

        $this->assertEquals(self::TYPE, $object->getType());
        $this->assertEquals(self::TITLE, $object->getTitle());
        $this->assertEquals(self::DESC, $object->getDescription());
        $this->assertEquals(self::ICON_PATH, $object->getIconPath());
        $this->assertEquals(self::IS_ONLINE, $object->isOnline());
        $this->assertEquals(self::ORDER_NUMBER, $object->getOrderNumber());
        $this->assertEquals($object->getPostCondition(), $this->post_condition);
        $this->assertEquals(self::REF_ID, $object->getRefId());

        $this->assertEquals(self::TYPE, $new_obj->getType());
        $this->assertEquals(self::TITLE, $new_obj->getTitle());
        $this->assertEquals(self::DESC, $new_obj->getDescription());
        $this->assertEquals(self::ICON_PATH, $new_obj->getIconPath());
        $this->assertEquals(false, $new_obj->isOnline());
        $this->assertEquals(self::ORDER_NUMBER, $new_obj->getOrderNumber());
        $this->assertEquals($new_obj->getPostCondition(), $this->post_condition);
        $this->assertEquals(self::REF_ID, $new_obj->getRefId());
    }

    /**
     * @depends testCreate
     */
    public function testWithOrderNumber(LSItem $object)
    {
        $new_obj = $object->withOrderNumber(20);

        $this->assertEquals(self::TYPE, $object->getType());
        $this->assertEquals(self::TITLE, $object->getTitle());
        $this->assertEquals(self::DESC, $object->getDescription());
        $this->assertEquals(self::ICON_PATH, $object->getIconPath());
        $this->assertEquals(self::IS_ONLINE, $object->isOnline());
        $this->assertEquals(self::ORDER_NUMBER, $object->getOrderNumber());
        $this->assertEquals($object->getPostCondition(), $this->post_condition);
        $this->assertEquals(self::REF_ID, $object->getRefId());

        $this->assertEquals(self::TYPE, $new_obj->getType());
        $this->assertEquals(self::TITLE, $new_obj->getTitle());
        $this->assertEquals(self::DESC, $new_obj->getDescription());
        $this->assertEquals(self::ICON_PATH, $new_obj->getIconPath());
        $this->assertEquals(self::IS_ONLINE, $new_obj->isOnline());
        $this->assertEquals(20, $new_obj->getOrderNumber());
        $this->assertEquals($new_obj->getPostCondition(), $this->post_condition);
        $this->assertEquals(self::REF_ID, $new_obj->getRefId());
    }

    /**
     * @depends testCreate
     */
    public function testWithPostCondition(LSItem $object)
    {
        $pc = new ilLSPostCondition(555, 'always');
        $new_obj = $object->withPostCondition($pc);

        $this->assertEquals(self::TYPE, $object->getType());
        $this->assertEquals(self::TITLE, $object->getTitle());
        $this->assertEquals(self::DESC, $object->getDescription());
        $this->assertEquals(self::ICON_PATH, $object->getIconPath());
        $this->assertEquals(self::IS_ONLINE, $object->isOnline());
        $this->assertEquals(self::ORDER_NUMBER, $object->getOrderNumber());
        $this->assertEquals($object->getPostCondition(), $this->post_condition);
        $this->assertEquals(self::REF_ID, $object->getRefId());

        $this->assertEquals(self::TYPE, $new_obj->getType());
        $this->assertEquals(self::TITLE, $new_obj->getTitle());
        $this->assertEquals(self::DESC, $new_obj->getDescription());
        $this->assertEquals(self::ICON_PATH, $new_obj->getIconPath());
        $this->assertEquals(self::IS_ONLINE, $new_obj->isOnline());
        $this->assertEquals(self::ORDER_NUMBER, $new_obj->getOrderNumber());
        $this->assertEquals($new_obj->getPostCondition(), $pc);
        $this->assertEquals(self::REF_ID, $new_obj->getRefId());
    }
}
