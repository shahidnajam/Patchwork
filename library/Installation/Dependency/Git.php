<?php
/**
 * Check out from a git repo
 *
 * @package    Installation
 * @subpackage Dependency
 * @author     Daniel Pozzi
 */
class Installation_Dependency_Git extends Installation_Dependency
{
    const COMMAND = 'git clone --depth 1 %s %s';

    /**
     *
     * @var string
     */
    private $origin;
    private $target;
    private $subPath;

    /**
     * Constructor
     * 
     * @param string $origin  repository url
     * @param string $target  target path with trailing slash
     * @param string $subPath part of repo to copy, can be omitted to copy all
     */
    public function  __construct($origin, $target, $subPath = '')
    {
        $this->origin = $origin;
        $this->target = $target;
        $this->subPath = $subPath;
    }

    /**
     * run.
     *
     *
     * 
     */
    public function install()
    {
        $tmp = sys_get_temp_dir().'/'.md5($this->origin).'/';
        $this->execute('rm -frd '.$tmp);
        $this->execute(sprintf(self::COMMAND, $this->origin, $tmp));

        $this->execute('rm -frd '.$tmp .'.git');
        $this->execute('cp -r '.$tmp . $this->subPath . ' '.$this->target);
    }
}