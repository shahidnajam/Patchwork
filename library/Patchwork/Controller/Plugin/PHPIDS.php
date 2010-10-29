<?php

/**
 * Patchwork
 *
 * @category Library
 * @package Patchwork
 * @subpackage Security
 * @author Daniel Pozzi <bonndan76@googlemail.com>
 */

/**
 * PHPIDS plugin
 *
 * requires
 * - that config includes a "phpids" section
 * - IDS to be autoloadable (e.g. in library)
 * <code>
  phpids.impact_threshold = 10
  phpids.impact_controller = error
  phpids.impact_action = badrequest
  phpids.General.filter_type     = xml
  phpids.General.base_path       = LIBRARY_PATH "/IDS/"
  phpids.General.use_base_path   = false
  phpids.General.filter_path     = LIBRARY_PATH "/IDS/default_filter.xml"
  phpids.General.tmp_path        = TMP_PATH
  phpids.General.scan_keys       = false
  phpids.General.HTML_Purifier_Path	= vendors/htmlpurifier/HTMLPurifier.auto.php
  phpids.General.HTML_Purifier_Cache = vendors/htmlpurifier/HTMLPurifier/DefinitionCache/Serializer
  phpids.General.html[]          = POST.__wysiwyg
  phpids.General.json[]          = POST.__jsondata
  phpids.General.exceptions[]    = GET.__utmz
  phpids.General.exceptions[]    = GET.__utmc
  phpids.General.min_php_version = 5.1.6
  phpids.Logging.path            = TMP_PATH "/logs/ids.log"
  phpids.Caching.caching         = file
  phpids.Caching.expiration_time = 600
  phpids.Caching.path            = TMP_PATH "/default_filter.cache"
 * </code>
 * @package Patchwork
 * @subpackage Security
 * @author Daniel Pozzi <bonndan76@googlemail.com>
 */
class Patchwork_Controller_Plugin_PHPIDS extends Zend_Controller_Plugin_Abstract
{
    /**
     * where to find the phpids settings in the config
     */
    const PHPIDS_CONFIG_SECTION = 'phpids';

    /**
     * scans the request
     *
     * @param Zend_Controller_Request_Abstract $request
     */
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        $input = array(
            'REQUEST' => $_REQUEST,
            'GET' => $_GET,
            'POST' => $_POST,
            'COOKIE' => $_COOKIE
        );
        $init = IDS_Init::init();
        $init->setConfig(Zend_Registry::get(Patchwork::CONFIG_REGISTRY_KEY)
            ->{self::PHPIDS_CONFIG_SECTION}->toArray()
        );

        // run the ids monitor
        $ids = new IDS_Monitor($input, $init);
        $result = $ids->run();
        // analyze the result
        if (!$result->isEmpty()) {
            $impact = $result->getImpact();
            // log the event
            $compositeLog = new IDS_Log_Composite();
            $compositeLog->addLogger(IDS_Log_File::getInstance($init));
            $compositeLog->execute($result);
            // for high impact, we redirect to error page
            if ($impact >= $config->impact_threshold) {
                $request->setControllerName($config->impact_controller);
                $request->setActionName($config->impact_action);
            }
        }
    }

}
