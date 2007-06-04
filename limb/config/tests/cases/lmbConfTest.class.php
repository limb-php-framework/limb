<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbConfTest.class.php 5933 2007-06-04 13:06:23Z pachanga $
 * @package    $package$
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
}

?>