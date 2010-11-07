<?php
/**
 * Patchwork
 *
 * @package    Testing
 * @subpackage Test
 * @author     Daniel Pozzi <bonndan76@googlemail.com>
 */
require_once(dirname(dirname(dirname(dirname(__FILE__))))).'/bootstrap.php';

/**
 * Acl ini test
 *
 * @package    Testing
 * @subpackage Test
 * @author     Daniel Pozzi <bonndan76@googlemail.com>
 */
class Patchwork_Acl_IniTest extends ControllerTestCase
{
    /**
     * test ini file
     * @var string
     */
    private $iniFile;

    public function  setUp()
    {
        parent::setUp();
        $this->iniFile = dirname(__FILE__) . '/../test.ini';
    }

    /**
     * 
     */
    public function testConstructorReadsAllSections()
    {
        $config = new Patchwork_Acl_Ini($this->iniFile);

        $this->assertTrue($config->areAllSectionsLoaded());
        $array = $config->toArray();
        $this->assertEquals(3, count($config));
        $this->assertTrue(isset($config->guest));

        $extends = $config->getExtends();
        $this->assertEquals(2, count($extends));
    }

    public function testAddConfigToAcl()
    {
        $acl = new Zend_Acl;

        $config = new Patchwork_Acl_Ini($this->iniFile);
        $config->addConfigToAcl($acl);

        $this->assertTrue($acl->hasRole('guest'));
        $this->assertTrue($acl->hasRole('user'));
        $this->assertTrue($acl->hasRole('admin'));
        $this->assertTrue($acl->has('howto_index'));
    }
}