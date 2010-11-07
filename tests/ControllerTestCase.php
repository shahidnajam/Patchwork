<?php
/**
 * Patchwork
 *
 * @package    Tests
 * @subpackage Testcase
 * @author     Daniel Pozzi <dpozzi@m2soft.com>
 */
require_once 'Zend/Application.php';
require_once 'Zend/Test/PHPUnit/ControllerTestCase.php';

/**
 * ControllerTestCase
 *
 * launches bootstrapping
 *
 * @category Testing
 * @package  Controller
 */
abstract class ControllerTestCase extends Zend_Test_PHPUnit_ControllerTestCase
{

    /**
     * app instance
     * @var Zend_Application
     */
    public $application;

    public function setUp()
    {
        Zend_Controller_Front::getInstance()->resetInstance();
        /**
         * @see http://framework.zend.com/issues/browse/ZF-10607
         */
        $this->bootstrap = new Zend_Application(
            APPLICATION_ENV,
            APPLICATION_PATH . '/configs/application.ini'
        );

        parent::setUp();
        /**
         * @see http://framework.zend.com/issues/browse/ZF-8193
         */
        $this->frontController->setParam('bootstrap', $this);

        
        Doctrine::createTablesFromModels();
        Doctrine::loadData(APPLICATION_PATH . '/doctrine/fixtures');
    }

    /**
     * tear down
     */
    public function tearDown()
    {
        Doctrine_Manager::getInstance()->closeConnection(Doctrine_Manager::connection());

        Zend_Controller_Front::getInstance()->resetInstance();
        $this->resetRequest();
        $this->resetResponse();

        $this->_frontController->setControllerDirectory(
            '/application/controllers', 'application'
        );
        $this->request->setPost(array());
        $this->request->setQuery(array());
    }

    /**
     * dispatch
     * 
     * @param string $url
     * @param string $method request method
     * @link http://blog.fedecarg.com/2008/12/27/testing-zend-framework-controllers/
     */
    public function dispatch(
        $url = null,
        $method = 'GET',
        array $requestArgs = null
    )
    {
        // redirector should not exit
        $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
        $redirector->setExit(false);

        // json helper should not exit
        $json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
        $json->suppressExit = true;

        $request = $this->getRequest();
        if (null !== $url) {
            $request->setRequestUri($url);
        }
        $request->setPathInfo(null);

        /**
         * add payload and method
         */
        $request->setMethod($method);
        $_SERVER['REQUEST_METHOD'] = $method;
        $_SERVER['HTTP_HOST'] = 'testhost';
        if (is_array($requestArgs))
            foreach ($requestArgs as $key => $value)
                $request->setParam($key, $value);

        
        $this->frontController->setDefaultModule('default');
        $this->frontController->addModuleDirectory(APPLICATION_PATH);
        
        $this->getFrontController()
            ->setRequest($request)
            ->setResponse($this->getResponse())
            ->throwExceptions(true)
            ->returnResponse(false);

        $this->bootstrap->run();
    }

    /**
     *
     * @return Patchwork_Container
     */
    public function getContainer()
    {
        return $this->bootstrap->getBootstrap()->getContainer();
    }
}