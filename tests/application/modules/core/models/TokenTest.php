<?php
/**
 * Patchwork
 *
 * @category Testing
 * @package  Model
 * @author   Daniel Pozzi <bonndan76@googlemail.com>
 */
require_once(dirname(dirname(dirname(dirname(dirname(__DIR__)))))).'/tests/bootstrap.php';

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
            ->bindImplementation('Patchwork_Token', 'Core_Model_Token')
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
    public function testGetTriggeredServiceName()
    {
        $token = $this->getToken();
        $token->setMultipleUse(true);
        $this->assertInstanceOf('Patchwork_Token', $token);
        
        $res = $token->getTriggeredServiceName();
        $this->assertEquals('ServiceX', $res);
    }

    /**
     * test that service is triggered
     *
     * @expectedException Patchwork_Token_Service_Exception
     */
    public function testGetTriggeredServiceException()
    {
        $token = new BadToken;
        $token->setContext(array('test' => '123'));
        $token->setBadService('NotAService');
        $token->save();
        $this->assertInstanceOf('Patchwork_Token', $token);

        $res = $this->service->trigger($token->getHash());
        $this->assertInstanceOf('Patchwork_Token_Triggered', $res);
    }

    public function testTrigger()
    {
        $token = $this->getToken(array('test' => '123'));
        $token->save();
        $res = $this->service->trigger($token->getHash());
        $this->assertInstanceOf('Patchwork_Token_Triggered', $res);
    }

    public function testMultipleUseDoesNotDeleteAfterTrigger()
    {
        $token = $this->getToken(array('test' => '123'));
        $token->setMultipleUse(true);
        $this->service->trigger($token->getHash());
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

class BadToken extends Core_Model_Token
{
    /**
     * set the service to be triggered
     *
     * @param Patchwork_Token_Triggered $service service instance
     */
    function setBadService($service)
    {
        $this->service = $service;
    }
}