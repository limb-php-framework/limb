<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/config/src/lmbConf.class.php');

class lmbConfTest extends UnitTestCase
{
  function testGet()
  {
    $conf = new lmbConf(dirname(__FILE__) . '/conf.php');
    $this->assertEqual($conf->get('foo'), 1);
    $this->assertEqual($conf->get('bar'), 2);
  }

  function testOverride()
  {
    $conf = new lmbConf(dirname(__FILE__) . '/other.conf.php');
    $this->assertEqual($conf->get('foo'), 3);
    $this->assertEqual($conf->get('bar'), 2);
  }

  function testImplementsIterator()
  {
    $conf = new lmbConf(dirname(__FILE__) . '/conf.php');

    $result = array();
    foreach($conf as $key => $value)
      $result[$key] = $value;

    $this->assertEqual($result, array('foo' => 1, 'bar' => 2));
  }

  function testGetNotExistedOption()
  {
    $conf = new lmbConf(dirname(__FILE__) . '/conf.php');

    try {
      $conf->get('some_not_existed_option');
      $this->fail();
    }
    catch (lmbNoSuchPropertyException $e)
    {
      $this->pass();
    }
  }
}