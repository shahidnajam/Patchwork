<?php

require_once(dirname(dirname(dirname(dirname(__FILE__))))).'/bootstrap.php';
/**
 *
 *
 *
 */
class Patchwork_JSON_DecoratorTest extends ControllerTestCase
{
    public function testWithSerializable()
    {
        $class = new JSON_Stub();
        $decorator = new Patchwork_JSON_Decorator($class);

        $this->assertEquals('"test"', $decorator->__toString());
    }

    public function testWithToArray()
    {
        $class = new ToArray_Stub();
        $decorator = new Patchwork_JSON_Decorator($class);

        $this->assertEquals('{"one":"two"}', $decorator->__toString());
    }

    public function testWithArray()
    {
        $decorator = new Patchwork_JSON_Decorator(array('one' => 'two'));

        $this->assertEquals('{"one":"two"}', $decorator->__toString());
    }
}

class JSON_Stub implements Patchwork_JSON_Serializable
{
    public function toJSON()
    {
        return json_encode('test');
    }
}

class ToArray_Stub
{
    public function toArray()
    {
        return array('one' => 'two');
    }
}