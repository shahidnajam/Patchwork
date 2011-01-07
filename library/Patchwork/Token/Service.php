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
     * constructor
     * 
     * @param Patchwork_Storage_Service $storageService
     * @param Patchwork_Token           $token
     */
    public function  __construct(
        Patchwork_Storage_Service $storageService,
        Patchwork_Token $token
    ) {
        $this->storageService = $storageService;
        $this->tokenClass = get_class($token);
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
     *
     * @param Patchwork_Token $token 
     * @todo use Container?
     */
    private function getTriggeredServiceFromToken(Patchwork_Token $token)
    {
        $service = $token->getTriggeredService();
        if (!$service instanceof Patchwork_Token_Triggered) {
            throw new RuntimeException(
                $className . ' does not implement TokenTriggered',
                500
            );
        }
    }

    /**
     * find a token
     * 
     * @param string $hash
     * @return Patchwork_Token|null
     */
    public function trigger($hash)
    {
        $token = $this->storageService->find($this->tokenClass, $hash);
        if ($token instanceof Patchwork_Token) {
            $service = $this->getTriggeredServiceFromToken($token);
            if (!$token->isMultiplyUsable()) {
                $this->storageService->delete($token);
            }
            
            return $service->startWithToken($token);
        }
    }
}