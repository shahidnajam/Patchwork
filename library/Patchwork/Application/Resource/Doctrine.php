<?php

/**
 *
  /**
 * Doctrine Application Resource
 *
 * @author Daniel Pozzi <bonndan76@googlemail.com>
 *
 */
class Patchwork_Application_Resource_Doctrine extends Zend_Application_Resource_ResourceAbstract
{

    /**
     * starting doctrine
     *
     * @return Doctrine_Connection
     */
    public function init()
    {
        // retrieves the options &  converts them to an Zend_Config Object
        $config = new Zend_Config( $this->getOptions() );
        if(!isset($config->options) || empty($config->options)) {
            throw new Zend_Application_Resource_Exception('Missing Configuration!');
        }

        require_once LIBRARY_PATH . DIRECTORY_SEPARATOR . 'Doctrine.php';
        $manager = Doctrine_Manager::getInstance();
        $manager->setAttribute(Doctrine::ATTR_AUTO_ACCESSOR_OVERRIDE, true);
        $manager->setAttribute(Doctrine::ATTR_USE_DQL_CALLBACKS, true);

        /**
         * caching
         */
        $cacheConn = Doctrine_Manager::connection(new PDO('sqlite::memory:'));
        $cacheDriver = new Doctrine_Cache_Db(
                array('connection' => $cacheConn, 'tableName' => 'cache')
        );
        $cacheDriver->createTable();
        $manager->setAttribute(Doctrine_Core::ATTR_QUERY_CACHE, $cacheDriver);

        /**
         * autoloading
         */
        /* $manager->setAttribute(
          Doctrine::ATTR_MODEL_LOADING,
          Doctrine::MODEL_LOADING_CONSERVATIVE
          ); */
        //$manager->setAttribute(Doctrine::ATTR_AUTOLOAD_TABLE_CLASSES, true);
        Doctrine::loadModels($config->options->models_path);
        Doctrine::loadModels($config->options->models_path . '/generated');

        Zend_Controller_Action_HelperBroker::addPrefix('Patchwork_Controller_Helper');

        $conn = Doctrine_Manager::connection(
                $config->connections->db, 'doctrine'
        );
        $conn->setAttribute(Doctrine::ATTR_USE_NATIVE_ENUM, true);
        $conn->setAttribute(Doctrine::ATTR_DEFAULT_TABLE_CHARSET,'utf8');
        $manager->setCharset('utf8');
        $manager->setCollate('utf8_general_ci');
        return $conn;
    }

}
