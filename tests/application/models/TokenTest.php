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
    public function testFactory()
    {
        $res = Token::factory('Nothing');
        $this->assertTrue($res instanceof Token);
        $this->assertNotNull($res->hash);
        $this->assertEquals('Nothing', $res->service);
        $this->assertGreaterThan(0, $res->id);
        $this->assertTrue($res->once > 0);
    }

    /**
     * test context serialisatoin
     * 
     * @param mixed $data
     * @dataProvider contextProvider
     */
    public function testGetContext($data)
    {
        $token = Token::factory('X', $data);
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
            array( 'string' ),
            array( array('an' => 'array') ),
            array( null ),
            array( new stdClass() )
        );
    }

    /**
     * test that service is triggered
     */
    public function testTriggerService()
    {
        $token = Token::factory('ServiceX', array('some' => 'data'));
        $res = $token->trigger();
        $this->assertTrue($res instanceof ServiceX);
        $this->assertGreaterThan(0, strtotime($token->deleted_at));
    }

    public function testMultipleUseDoesNotDeleteAfterTrigger()
    {
        $token = Token::factory('ServiceX', array('some' => 'data'), true);
        $token->trigger();
        $this->assertNull($token->deleted_at);
    }
}

/**
 * Stub
 * 
 */
class ServiceX implements TokenTriggered
{
    /**
     *
     * @param Token $token
     * @return <type>
     */
    public function  startWithToken(Token $token)
    {
        return $this;
    }
}