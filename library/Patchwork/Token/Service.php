<?php
/**
 * Patchwork
 *
 * @package    Patchwork
 * @subpackage Token
 * @author     Daniel Pozzi
 */

/**
 * Token service
 *
 * @package    Patchwork
 * @subpackage Token
 * @author     Daniel Pozzi
 */
class Patchwork_Token_Service
{
    /**
     * storage Service
     * @var Patchwork_Storage_Service
     */
    private $storageService;

    /**
     * class name
     * @var string
     */
    private $tokenClass;

    /**
     *
     * @var Patchwork_Container
     */
    private $container;

    /**
     * constructor
     * 
     * @param Patchwork_Storage_Service $storageService
     * @param Patchwork_Token           $token
     */
    public function  __construct(
        Patchwork_Storage_Service $storageService,
        Patchwork_Token $token,
        Patchwork_Container $container
    ) {
        $this->storageService = $storageService;
        $this->tokenClass = get_class($token);
        $this->container = $container;
    }

    /**
     * get new token
     * 
     * @return Patchwork_Token
     */
    private function getNewToken()
    {
        return new $this->tokenClass;
    }

    /**
     * factory method
     *
     * @param string  $service triggered service
     * @param mixed   $context  context data
     * @param boolean $multiple multiple usage flag (default is false)
     *
     * @return Patchwork_Token
     */
    public function createToken($service, array $context, $multiple = false)
    {
        $token = $this->getNewToken();
        $token->setTriggeredService($service);
        $token->setContext($context);
        $token->setMultipleUse($multiple);

        $this->storageService->save($token);

        return $token;
    }

    /**
     * get an instance of the service associated to the token
     * 
     * @param Patchwork_Token $token
     * @return Patchwork_Token_Triggered
     */
    private function getTriggeredServiceFromToken(Patchwork_Token $token)
    {
        $serviceName = $token->getTriggeredServiceName();
        $service = $this->container->getInstance($serviceName);
        if (!$service instanceof Patchwork_Token_Triggered) {
            throw new Patchwork_Token_Service_Exception(
                $className . ' does not implement TokenTriggered', 500
            );
        }

        return $service;
    }

    /**
     * find a token and trigger the service 
     * 
     * @param string $hash
     * @return Patchwork_Token
     * @throws Patchwork_Token_Service_Exception
     */
    public function trigger($hash)
    {
        $token = $this->storageService
            ->findWhere($this->tokenClass, array('hash' =>$hash));
        if ($token instanceof Patchwork_Token) {
            $service = $this->getTriggeredServiceFromToken($token);
            if (!$token->isMultipleUsable()) {
                $this->storageService->delete($token);
            }
            
            return $service->startWithToken($token);
        }

        throw new Patchwork_Token_Service_Exception(
            "Could not find {$this->tokenClass} $hash", 404
        );
    }
}