<?php

require_once 'Zend/Application.php';
require_once 'Zend/Test/PHPUnit/ControllerTestCase.php';

/**
 * ControllerTestCase
 *
 * launches bootstrapping
 *
 * 
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
        $this->application = new Zend_Application(
                APPLICATION_ENV,
                APPLICATION_PATH . '/configs/application.ini'
        );

        $this->bootstrap = array($this, 'appBootstrap');
        parent::setUp();
    }

    /**
     *
     */
    public function appBootstrap()
    {
        $this->application->bootstrap();
        /*Doctrine::loadModels(MODELS_PATH .'/generated');
        Doctrine::loadModels(MODELS_PATH);*/
        Doctrine::createTablesFromModels();
        Doctrine::loadData(APPLICATION_PATH . '/doctrine/data/fixtures');
    }

    public function tearDown()
    {
        Doctrine_Manager::getInstance()->closeConnection(Doctrine_Manager::connection());
    }

}