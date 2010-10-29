<?php

class Patchwork_Acl_Ini extends Zend_Config_Ini
{
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
                        $config['_extends'] = $value;
                    }
                } else {
                    /**
                     * @see Zend_Config_Exception
                     */
                    // require_once 'Zend/Config/Exception.php';
                    throw new Zend_Config_Exception("Parent section '$section' cannot be found");
                }
            } else {
                $config = $this->_processKey($config, $key, $value);
            }
        }
        return $config;
    }

    /**
     * name of the extended role
     * 
     * @return string|null
     */
    public function getExtendedRole()
    {
        if(isset($this->_extends))
            return current($this->_extends);
    }
}