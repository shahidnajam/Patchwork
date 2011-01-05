<?php
/**
 * Installation self update
 *
 * @package    Installation
 * @subpackage Dependency
 * @author     Daniel Pozzi
 */
class Installation_Dependency_SelfUpdate extends Installation_Dependency_Git
{
    public function  __construct()
    {
        parent::__construct(
            self::OWN_REPO,
            dirname(dirname(__DIR__)), //which is above Installation/Dependency
            'Installation'
        );
    }
}