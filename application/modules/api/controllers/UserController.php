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

    public $modelName = 'User_Model_User';

    /**
     * list
     *
     * the standard request, "search" param allows to search for username
     */
    public function indexAction()
    {
        $where = array();
        $username = $this->_getParam(self::SEARCH_USERNAME_PARAM);
        if($username != ''){
            $where['username LIKE ?'] = $username.'%';
        }

        $objects = $this->getStorageService()->fetch(
            $this->modelName,
            $where,
            $this->_getOrderBy(),
            $this->_getLimit(),
            $this->_getOffset()
        );

        $this->getResponse()
            ->setHttpResponseCode(200)
            ->setHeader('Content-type', 'application/json')
            ->appendBody(new Patchwork_Decorator_JSON($objects));
    }
}
