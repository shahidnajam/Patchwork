<?php

class Patchwork_DomainLog_Writer extends Zend_Log_Writer_Abstract
{
    /**
     *
     * @var Patchwork_DomainLog_Model
     */
    private $logModel;

    /**
     * construct
     * 
     * @param Patchwork_Storage_Service $service 
     */
    public function  __construct(Patchwork_DomainLog_Model $model)
    {
        $this->logModel = $model;
    }

    /**
     * write event if it is a domain event
     * 
     * @param array $event
     */
    protected function  _write($event)
    {
        if ($event['priority'] === Patchwork_DomainLog_Service::DOMAIN) {
            $this->logModel->createFromEvent($event);
        }
    }
}