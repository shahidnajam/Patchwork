<?php
/**
 * Handle dependencies
 * 
 *
 */
abstract class Installation_Dependency
{
    const OWN_REPO = 'git://github.com/bonndan/Installation.git';
    
    const GIT = 'git';

    /**
     * deps
     * @var array
     */
    protected $dependencies = array();

    protected function getTempDirForCheckout()
    {

    }
    
    protected function execute($command)
    {
        $returnVal = NULL;
        system($command, $returnVal);
        
        return $returnVal;
    }
}
