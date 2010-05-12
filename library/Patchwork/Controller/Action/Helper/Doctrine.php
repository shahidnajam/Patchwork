<?php
/**
 * Controller helper to fetch Doctrine models
 * derived from CU_Controller_Action_Helper_Doctrine by Jani Hartikainen
 *
 * @package Patchwork
 * @subpackage Controller
 */
class Patchwork_Controller_Action_Helper_Doctrine
    extends Zend_Controller_Action_Helper_Abstract
{
    /**
     * standard redirect
     * @var array
     */
	protected $_redirect = array(
		'action' => 'index',
		'controller' => null,
		'module' => null
	);

	/**
	 * Set the redirection target
	 * @param $action string action name
	 * @param $controller string controller name
	 * @param $module string module name
	 * @return CU_Controller_Action_Helper_Doctrine implements a fluent interface
	 */
	public function setRedirect(
        $action,
        $controller = null,
        $module = null
    ){
		$this->_redirect['action'] = $action;
		$this->_redirect['controller'] = $controller;
		$this->_redirect['module'] = $module;

		return $this;
	}

	/**
	 * Fetch record from DB or redirect
     *
	 * @param $model string Name of the model class
	 * @param $id int the record's PK
	 * @return Doctrine_Record
	 */
	public function getRecordOrRedirect($model, $id)
	{
		$record = Doctrine::getTable($model)->find($id);
		if($record == false)
		{
			$this->getActionController()->getHelper('Redirector')->goto(
				$this->_redirect['action'],
				$this->_redirect['controller'],
				$this->_redirect['module']
			);
		}

		return $record;
	}

	/**
	 * Fetch record from DB or throw a not found exception
     *
	 * @param $model string Name of the model class
	 * @param $id int the record's PK
     *
	 * @return Doctrine_Record
     * @throws Exception
	 */
	public function getRecordOrException($model, $id)
	{
		if(!$record = Doctrine::getTable($model)->find($id))
			throw new Exception('Model not found '.$model.' '.$id);

		return $record;
	}

    /**
	 * Fetch record from DB
     *
	 * @param $model string Name of the model class
	 * @param $id int the record's PK
     *
	 * @return Doctrine_Record|null
	 */
	public function getRecord($model, $id)
	{
		return Doctrine::getTable($model)->find($id);
	}
}