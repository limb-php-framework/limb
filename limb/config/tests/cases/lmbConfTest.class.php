<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
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
      $conf = new lmbConf(dirname(__FILE__) . '/not_existed.php');
      $this->fail();
    }
    catch (lmbFileNotFoundException $e)
    {
      $this->pass();
    }
  }

  function testGetNotExistedOption()
  {
    $conf = new lmbConf(dirname(__FILE__) . '/conf.php');
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

  function testMultipleFiles()
  {
    $conf = new lmbConf(array(
      dirname(__FILE__) . '/higher_settings/test.conf.php',
      dirname(__FILE__) . '/lower_settings/test.conf.php'
    ));
    $this->assertEqual($conf->get('foo'), array(
      'bar' => 42
    ));
    $this->assertEqual($conf->get('baz'), true);
    $pool_settings = $conf->get('some_pool');
    $this->assertEqual(count($pool_settings), 2);
    $this->assertEqual($pool_settings[0]['value'], 100);
    $this->assertEqual($pool_settings[1]['value'], 2);
  }

  function testLowerConfAppendsToEnd()
  {
    $conf = new lmbConf(array(
      dirname(__FILE__) . '/higher_settings/test.conf.php',
      dirname(__FILE__) . '/lower_settings/test.conf.php'
    ));
    $merged = array(
      'some_pool' => array(
        array(
          'value' => 100
        ),
        array(
          'value' => 2
        )
      ),
      'higher_numeric_value',
      'foo' => array(
        'bar' => 42
      ),
      'baz' => true
    );
    $this->assertIdentical($merged, $conf->export());
  }
}
