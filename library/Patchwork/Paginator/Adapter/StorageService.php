<?php
/**
 * Patchwork_Paginator_Adapter_StorageService
 *
 * <code>
 * $adapter = $container->getInstance('Patchwork_Paginator_Adapter_StorageService');
 * $adapter->setParams('MyModel', array('name LIKE' => 'a%'), 'name ASC');
 * $paginator->setAdapter($adapter);
 */
class Patchwork_Paginator_Adapter_StorageService
implements Zend_Paginator_Adapter_Interface
{

    /**
     * storage service
     * @var Patchwork_Storage_Service
     */
    private $storageService;
    /**
     *
     * @var int
     */
    private $totalCount = 0;
    private $model;
    private $where;
    private $order;

    /**
     * constructor
     * @param Patchwork_Storage_Service $service storage service instance
     */
    public function __construct(Patchwork_Storage_Service $service)
    {
        $this->storageService = $service;
    }

    /**
     *
     * @param <type> $model
     * @param array $where
     * @param <type> $order
     */
    public function setParams($model, array $where = null, $order = null)
    {
        $this->model = $model;
        $this->where = $where;
        $this->order = $order;

        return $this;
    }

    /**
     * get items
     * 
     * @param int $offset
     * @param int $itemCountPerPage
     */
    public function getItems($offset, $itemCountPerPage)
    {
        return $this->storageService->fetch(
            $this->model,
            $this->where,
            $this->order,
            $itemsPerPage,
            $offset
        );
    }

    /**
     * total number of rows
     *
     * @return int
     */
    public function count()
    {
        return $this->totalCount;
    }

    /**
     * set the total number of rows
     * 
     * @param int $count
     * @return Patchwork_Paginator_Adapter_StorageService
     */
    public function setCount($count)
    {
        $this->totalCount = $count;
        return $this;
    }

}
