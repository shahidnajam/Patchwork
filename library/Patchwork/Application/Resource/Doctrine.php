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
    const QUERY_CACHE_TABLE = 'doctrine_query_cache';
    
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
        $manager->setAttribute(Doctrine_Core::ATTR_AUTO_FREE_QUERY_OBJECTS,true);

        /**
         * connect
         */
        $connection = Doctrine_Manager::connection(
                $config->connections->db, 'doctrine'
        );
        $connection->setAttribute(Doctrine::ATTR_USE_NATIVE_ENUM, true);
        $connection->setAttribute(Doctrine::ATTR_DEFAULT_TABLE_CHARSET,'utf8');
        $manager->setCharset('utf8');
        $manager->setCollate('utf8_general_ci');
        
        /**
         * caching using same connection
         */
        if($config->options->use_query_cache){
            $cacheDriver = new Doctrine_Cache_Db(
                array(
                    'connection' => $connection,
                    'tableName' => self::QUERY_CACHE_TABLE
                )
            );
            if(!$cacheDriver->getConnection()->import->tableExists(self::QUERY_CACHE_TABLE)) {
                $cacheDriver->createTable();
            }

            $manager->setAttribute(Doctrine_Core::ATTR_QUERY_CACHE,$cacheDriver);
        }

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

        return $connection;
    }

}
