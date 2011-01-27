<?php
/**
 * Patchwork Authenticator for databases
 *
 * @package    Patchwork
 * @subpackage Authentication
 * @author     Daniel Pozzi <bonndan76@googlemail.com>
 * 
 * <code>
 * $a = $this->getContainer()->Pachworkwork_DBAuthenticator;
 * $isLoggedIn = $a->authenticate($username, $password);
 * </code>
 */
class Patchwork_Auth_DBAuthenticator
{
    /**
     * adapter to use 
     * @var Patchwork_Auth_DBAdapter
     */
    private $adapter;
    /**
     * model
     * @var Patchwork_Auth_DBModel
     */
    private $model;

    /**
     * result
     * @var Zend_Auth_Result
     */
    private $result;

    /**
     * constructor needs auth adapter and auth model
     * 
     * @param Patchwork_Auth_DBAdapter $adapter
     * @param Patchwork_Auth_DBModel   $model
     */
    public function  __construct(
        Patchwork_Auth_DBAdapter $adapter,
        Patchwork_Auth_DBModel $model
    ) {
        $this->adapter = $adapter;
        $this->model   = $model;
    }

    /**
     * authenticate using Zend_Auth
     *
     * @param string $identity   identity
     * @param string $credential credential (password)
     *
     * @return boolean
     */
    public function authenticate($identity, $credential)
    {
        $this->adapter
            ->setIdentity($identity)
            ->setCredential($credential);
		$this->result = Zend_Auth::getInstance()->authenticate($this->adapter);

		/*
		 * save object on success
		 */
		if($this->result->isValid() && Zend_Auth::getInstance()->hasIdentity())
		{
			Zend_Auth::getInstance()->getStorage()->write(
                $this->adapter->getAuthModel()
            );
			return true;
		}
        return false;
    }
}
