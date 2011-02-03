<?php
/**
 * Patchwork_DomainLog_Model
 *
 * interface for domainlog entry models. the models is responsible for saving
 *
 * @package    Patchwork
 * @subpackage DomainLog
 */
interface Patchwork_DomainLog_Model
{
    /**
     * constructor
     * 
     * @param Patchwork_Storage_Service $service storage service instance
     */
    function __construct(Patchwork_Storage_Service $service);

    /**
     * creates with a Zend_Log event
     *
     * @param array $event event
     * @see Patchwork_DomainLog_Writer
     */
    function createFromEvent(array $event);
}