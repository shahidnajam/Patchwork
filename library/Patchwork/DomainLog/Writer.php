<?php
/**
 * Patchwork_DomainLog_Writer
 *
 * writer for Zend_Log which logs event with DOMAIN priority
 *
 * @package    Patchwork
 * @subpackage DomainLog
 * @author     Daniel Pozzi <bonndan76@googlemail.com>
 */
class Patchwork_DomainLog_Writer extends Zend_Log_Writer_Abstract
{
    /**
     * model
     * @var Patchwork_DomainLog_Model
     */
    private $logModel;

    /**
     * constructor
     * 
     * @param Patchwork_DomainLog_Model $model model instance
     */
    public function  __construct(Patchwork_DomainLog_Model $model)
    {
        $this->logModel = $model;
    }

    /**
     * write event if it is a domain event
     * 
     * @param array $event event
     */
    protected function  _write($event)
    {
        if ($event['priority'] === Patchwork_DomainLog_Service::DOMAIN) {
            $this->logModel->createFromEvent($event);
        }
    }

    /**
     * Construct a Zend_Log driver
     *
     * @param  array|Zen_Config $config
     * @return Zend_Log_FactoryInterface
     */
    static public function factory($config)
    {
        return Patchwork_Container::getBootstrapContainer()
            ->getInstance(__CLASS__);
    }
}