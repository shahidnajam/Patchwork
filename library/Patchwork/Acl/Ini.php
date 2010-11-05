<?php
/**
 * Patchwork
 *
 * @package    Patchwork
 * @subpackage Acl
 * @author     Daniel Pozzi <bonndan76@googlemail.com>
 */

/**
 * Patchwork_Acl_Ini
 *
 * reads a specifically styled ini file into a Patchwork_Acl configuration
 * 
 * @package    Patchwork
 * @subpackage Acl
 * @author     Daniel Pozzi <bonndan76@googlemail.com>
 */
class Patchwork_Acl_Ini extends Zend_Config_Ini
{
    /**
     * acl
     * @var Patchwork_Acl
     */
    private $_acl;
    /**
     * section that have been added to the acl
     * @var array
     */
    private $_sectionsAdded = array();

    /**
     * separates between resource and privilege
     * @var string
     */
    const ACTION_PRIVILEGE_SEPARATOR = '.';

    const EXTENDS_MARKER = '_extends';

    /**
     * Process each element in the section and handle the ";extends" inheritance
     * key. Passes control to _processKey() to handle the nest separator
     * sub-property syntax that may be used within the key name.
     *
     * @param  array  $iniArray
     * @param  string $section
     * @param  array  $config
     * @throws Zend_Config_Exception
     * @return array
     */
    protected function _processSection($iniArray, $section, $config = array())
    {
        $thisSection = $iniArray[$section];

        foreach ($thisSection as $key => $value) {
            if (strtolower($key) == ';extends') {
                if (isset($iniArray[$value])) {
                    $this->_assertValidExtend($section, $value);

                    if (!$this->_skipExtends) {
                        $config = $this->_processSection($iniArray, $value, $config);
                        $config[self::EXTENDS_MARKER] = $value;
                    }
                } else {
                    /**
                     * @see Zend_Config_Exception
                     */
                    throw new Zend_Config_Exception("$section cannot be found");
                }
            } else {
                $config = $this->_processKey($config, $key, $value);
            }
        }
        return $config;
    }

    /**
     * add roles, permissions and privileges to an acl
     *
     * @param Patchwork_Acl $acl acl
     * 
     * @return self
     */
    public function addConfigToAcl(Patchwork_Acl $acl)
    {
        $this->_acl = $acl;
        foreach($this as $section => $config){
            
            if(isset($this->_extends[$section])
                && !$acl->hasRole($this->_extends[$section])
            ){
                $this->_addSection($this->_extends[$section]);
            }
            $this->_addSection($section);
        }
    }

    /**
     * add settings for one specific role
     *
     * @param string $section section, is a role name
     *
     * @return Patchwork_Acl_Ini
     */
    protected function _addSection($section)
    {
        $extends = isset($this->_extends[$section])?$this->_extends[$section]:null;
        
        if (!$this->_acl->hasRole($section)) {
            $this->_acl->addRole($section, $extends);
        }

        foreach ($this->$section as $resource => $privileges) {
            if($resource == self::EXTENDS_MARKER){
                continue;
            }
            if(!$this->_acl->has($resource)) {
                $this->_acl->addResource(new Zend_Acl_Resource($resource));
            }
            
            foreach ($privileges as $privilege => $bool) {
                if($bool !== false){
                    $this->_acl->allow($section, $resource, $privilege);
                }
            }
        }

        return $this;
    }


}