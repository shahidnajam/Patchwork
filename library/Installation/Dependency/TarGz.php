<?php
/**
 * Check out from a git repo
 *
 * @package    Installation
 * @subpackage Dependency
 * @author     Daniel Pozzi
 */
class Installation_Dependency_TarGz extends Installation_Dependency
{
    const CURL_COMMAND = 'curl -O %s';
    const WGET_COMMAND = 'wget --no-check-certificate -O %s %s';
    const TAR_COMMAND = 'tar --strip-components=1 -xzv -f %s -C %s';

    /**
     * origin
     * @var string
     */
    private $origin;
    /**
     *
     * @var string
     */
    private $target;
    /**
     *
     * @var string
     */
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
     * 1) untar in tmp dir
     * 2) copy selected parts to target
     *
     *
     */
    public function install()
    {
        $tmp = sys_get_temp_dir().'/'.md5($this->origin).'/';
        $this->execute('rm -frd '.$tmp);
        $this->execute('mkdir '.$tmp);

        $fileName = basename($this->origin);
        $res = $this->execute(sprintf(self::CURL_COMMAND, $this->origin));
        if($res != 1) {
            $res = $this->execute(
                sprintf(self::WGET_COMMAND, $tmp.$fileName, $this->origin)
            );
        }
        
        $this->execute(sprintf(self::TAR_COMMAND, $tmp.$fileName, $tmp));
        $this->execute('rm '.$tmp.$fileName);

        $this->execute('cp -r '.$tmp . $this->subPath . ' '.$this->target);
    }
}