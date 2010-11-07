<?php

/**
 * Http Basic Auth Resolver using Doctrine
 * 
 * @author Daniel Pozzi
 * @see http://blog.simlau.net/zendframework-http-auth.html
 */
class Patchwork_Auth_Adapter_Http_Resolver_Doctrine
implements Zend_Auth_Adapter_Http_Resolver_Interface
{
    const AUTH_CLASS = 'User';

    /**
     * User
     * @var User|null
     */
    public $user;

    /**
     * auth against database
     * 
     * @param string $username username to look for
     * @param string $realm    not used
     *
     * @return string
     */
    public function resolve($username, $realm)
    {
        $user = Doctrine::getTable(self::AUTH_CLASS)->findOneByUsername($username);
        if ($user instanceof Doctrine_Record) {
            $this->user = $user;
            return $user->password;
        }
    }

    /**
     * salts a plain password using the users salt, same as credential 
     * treatment 
     *
     * @param <type> $password
     * 
     * @return string
     */
    public function saltPlainPassword($password)
    {
        if (!$this->user instanceof User)
            throw new Patchwork_Exception('No User object present in resolver');

        $salted = md5($password . $this->user->salt);
        return $salted;
    }

}