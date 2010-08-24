<?php
/**
 * REST controller related to Doctrine User
 *
 * @category Application
 * @package  API
 * @subpackage Controller
 * @author Daniel Pozzi <bonndan76@googlemail.com>
 */
class Api_UserController extends Patchwork_Controller_RESTModelController
{
    const SEARCH_USERNAME_PARAM = 'username';

    public $modelName = 'User';

    /**
     * list
     *
     * the standard request, "search" param allows to search for username
     */
    public function indexAction()
    {
        $offset = (int)$this->_getParam(self::OFFSET_PARAM);
        $limit  = (int)$this->_getParam(self::LIMIT_PARAM);
        $username = $this->_getParam(self::SEARCH_USERNAME_PARAM);

        
        $query = $this->_helper->Doctrine->listRecords(
            $this->modelName,
            $limit,
            $offset,
            true
        );

        if($username != ''){
            $query->where('username LIKE ?', $username.'%');

        }
        $objects = $query->execute();
        $this->getResponse()
            ->setHttpResponseCode(200)
            ->setHeader('Content-type', 'application/json')
            ->appendBody(
                $this->toJSON($objects)
            );
    }
}
