<?php
/**
 * Patchwork
 *
 * @category Testing
 * @package  Model
 * @author   Daniel Pozzi <bonndan76@googlemail.com>
 */
require_once(dirname(dirname(dirname(dirname(__FILE__))))).'/tests/bootstrap.php';

/**
 * Test for the token model
 *
 * @category Testing
 * @package  Models
 */
class TokenTest extends ControllerTestCase
{
    /**
     *
     * @var Patchwork_Token_Service
     */
    private $service;

    public function  setUp()
    {
        parent::setUp();
        $this->service = $this->getContainer()
            ->bindImplementation('Patchwork_Token', 'Token')
            ->getInstance('Patchwork_Token_Service');
    }

    /**
     *
     * @param int $data
     * @return Patchwork_Token
     */
    private function getToken($data = NULL)
    {
        if($data === NULL)
            $data = array('test' => 123);
        
        $token = $this->service->createToken(new ServiceX, $data);
        return $token;
    }

    /**
     * test context serialisatoin
     * 
     * @param mixed $data
     * @dataProvider contextProvider
     */
    public function testGetContext($data)
    {
        $token = $this->getToken($data);
        $this->assertInstanceOf('Patchwork_Token', $token);
        $this->assertEquals($data, $token->getContext());
    }

    /**
     * data provider
     * 
     * @return array
     */
    public function contextProvider()
    {
        return array(
            array( array('string') ),
            array( array('an' => 'array') ),
            array( array(new stdClass()) )
        );
    }

    /**
     * test that service is triggered
     */
    public function testGetTriggeredService()
    {
        $token = $this->getToken();
        $this->assertInstanceOf('Patchwork_Token', $token);
        $res = $token->getTriggeredService();
        $this->assertTrue($res instanceof ServiceX);
    }

    public function testMultipleUseDoesNotDeleteAfterTrigger()
    {
        $this->markTestIncomplete();
    }
}

/**
 * Stub
 * 
 */
class ServiceX implements Patchwork_Token_Triggered
{
    /**
     * 
     * @param Token $token
     * @return <type>
     */
    public function  startWithToken(Patchwork_Token $token)
    {
        return $this;
    }
}