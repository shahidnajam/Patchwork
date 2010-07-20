<?php

require_once(dirname(dirname(dirname(dirname(dirname(__FILE__)))))).'/bootstrap.php';
/**
 *
 *
 * 
 */
class Patchwork_View_Helper_RenderModelTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider viewPathProvider
     *
     *
     * 
     */
    public function testGetViewPath($model, $context, $suffix, $expected)
    {
        $res = Patchwork_View_Helper_RenderModel::getViewPath(
            $model,
            $context,
            $suffix
            );
        
        $this->assertContains($expected, $res);
    }

    /**
     * dataProvider
     * 
     * @return array
     */
    public function viewPathProvider()
    {
        return array(
            array('Test', null, null, 'test.phtml'),
            array('Test2', 'context', null, 'test2_context.phtml'),
        );
    }

    /**
     * test that renderModel return assigns model and other vars to Zend_View
     *
     *
     */
    public function testRenderModel()
    {
       
       $helper = new Patchwork_View_Helper_RenderModel;

       $model = new User;
       $args = array('test' => true);
       $context = 'test';
       
       $res = $helper->renderModel($model, $context, $args, true);

       $this->assertTrue($res instanceof Zend_View, 'Not a Zend_View '.get_class($res));
       $this->assertEquals($model, $res->User);
       $this->assertTrue($res->test);
    }
}
