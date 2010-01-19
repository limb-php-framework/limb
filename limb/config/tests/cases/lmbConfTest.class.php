<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/config/src/lmbConf.class.php');
lmb_require('limb/fs/src/lmbFs.class.php');

class lmbConfTest extends UnitTestCase
{


  protected function _getConfigPath($config_name)
  {
    return lmb_var_dir().'/configs/'.$config_name;
  }

  function _createConfig($name = 'conf.php', $content = false)
  {
    if(!$content)
    {
      $content = <<<EOD
<?php
\$conf = array('foo' => 1,
              'bar' => 2);
EOD;
    }
    lmbFs::safeWrite($this->_getConfigPath($name), $content);
    return new lmbConf($this->_getConfigPath($name));
  }

  function testGet()
  {
    $conf = $this->_createConfig();
    $this->assertEqual($conf->get('foo'), 1);
    $this->assertEqual($conf->get('bar'), 2);
  }

  function testOverride()
  {
    $content = <<<EOD
<?php
\$conf['foo'] = 1;
EOD;
    $this->_createConfig('conf.override.php', $content);
    $config = $this->_createConfig('conf.php');

    $this->assertEqual($config->get('foo'), 1);
    $this->assertEqual($config->get('bar'), 2);
  }

  function testImplementsIterator()
  {
   $conf = $this->_createConfig();
   $result = array();
   foreach ($conf as $key => $value)
     $result[$key] = $value;

   $this->assertEqual($result, array(
     'foo' => 1,
     'bar' => 2
   ));
  }

  function testGetNotExistedFile()
  {
    try
    {
      $conf = new lmbConf('not_existed.php');
      $this->fail();
    }
    catch (lmbFileNotFoundException $e)
    {
      $this->pass();
    }
  }

  function testGetNotExistedOption()
  {
    $conf = $this->_createConfig();
    try
    {
      $conf->get('some_not_existed_option');
      $this->fail();
    }
    catch (lmbNoSuchPropertyException $e)
    {
      $this->pass();
    }
  }

  function testGetWithoutOptionName()
  {
    $conf = $this->_createConfig();
    try
    {
      $this->assertEqual($conf->get(''), 1);
      $this->fail();
    }
    catch(lmbInvalidArgumentException $e)
    {
      $this->pass();
    }
  }

}
