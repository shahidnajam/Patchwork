<?php

/**
 * Http Basic Auth Resolver against
 * 
 * @author Daniel Pozzi
 * @see http://blog.simlau.net/zendframework-http-auth.html
 */
class Patchwork_Auth_Adapter_Http_Resolver_DB
implements Zend_Auth_Adapter_Http_Resolver_Interface
{
    /**
     * User
     * @var Patchwork_Auth_DBModel
     */
    private $authModel;

    /**
     *
     * @var Patchwork_Storage_Service
     */
    private $storageService;

    public function  __construct(
        Patchwork_Auth_DBModel $authModel,
        Patchwork_Storage_Service $storageService
    )
    {
        $this->authModel = $authModel;
        $this->storageService = $storageService;
    }


    /**
     * auth against database
     * 
     * @param string $username username to look for
     * @param string $realm    not used
     *
     * @return string password | false
     */
    public function resolve($username, $realm)
    {
        $modelName = get_class($this->authModel);
        $credCol = $this->authModel->getAuthenticationCredentialColumn();
        $where = array($credCol => $username);
        $user = $this->storageService->fetch($modelName, $where);
        if ($user instanceof Patchwork_Auth_DBModel) {
            $this->authModel = $user;
            return $this->authModel->getAuthenticationCredential();
        }

        return false;
    }

    /**
     * get the model instance
     * @return Patchwork_Auth_DBModel
     */
    public function getAuthModel()
    {
        return $this->authModel;
    }
}