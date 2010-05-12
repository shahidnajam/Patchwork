<?php
class CU_Auth_Adapter_Doctrine implements Zend_Auth_Adapter_Interface
{
	private $_model;
	private $_identityColumn;
	private $_credentialColumn;
	private $_identity;
	private $_credential;

	private $_result;

	public function setModelName($name)
	{
		$this->_model = $name;
		return $this;
	}

	public function setIdentityColumn($name)
	{
		$this->_identityColumn = $name;
		return $this;
	}

	public function setCredentialColumn($name)
	{
		$this->_credentialColumn = $name;
		return $this;
	}

	public function setIdentity($user)
	{
		$this->_identity = $user;
		return $this;
	}
	
	public function setCredential($password)
	{
		$this->_credential = $password;
		return $this;
	}

	public function authenticate()
	{
		$row = Doctrine_Query::create()
		     ->from($this->_model . ' u')
		     ->where('u.' . $this->_identityColumn . ' = ? AND u.' . $this->_credentialColumn . ' = ?', array($this->_identity, $this->_credential))
		     ->fetchOne();

		$authResult = array(
			'code' => Zend_Auth_Result::FAILURE,
			'identity' => $this->_identity,
			'messages' => array()
		);

		if($row !== false)
		{
			$authResult['code']= Zend_Auth_Result::SUCCESS;
			$this->_result = $row;
		}

		return new Zend_Auth_Result($authResult['code'], $authResult['identity'], $authResult['messages']);
	}

	public function getResultRowObject($returnColumns = null, $omitColumns = null)
	{
		return $this->_result;
	}
}
