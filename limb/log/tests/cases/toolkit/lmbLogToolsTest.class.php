<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/log/toolkit.inc.php');

class lmbLogToolsTest extends UnitTestCase
{
  protected $toolkit;

  function setUp()
  {
    lmbToolkit :: save();
    $this->toolkit = lmbToolkit :: merge(new lmbLogTools());
  }

  function tearDown()
  {
    lmbToolkit :: restore();
  }

  function testGetLogDSNes_default()
  {
    $dsnes = $this->toolkit->getLogDSNes();
    $this->assertEqual('file://'.realpath(lmb_env_get('LIMB_VAR_DIR')).'/log/error.log', $dsnes[0]);
  }

  function testGetLogDSNes_fromConfig()
  {
    $this->toolkit->setConf('common', array('logs' => array('foo')));

    $dsnes = $this->toolkit->getLogDSNes();
    $this->assertEqual('foo', $dsnes[0]);
  }

  function testGetLog()
  {
    $logs_conf = array('logs' => array('firePHP://localhost/?check_extension=0'));
    $this->toolkit->setConf('common', $logs_conf);

    $writer = current($this->toolkit->getLog()->getWriters());
    $this->assertIsA($writer, 'lmbLogFirePHPWriter');
    $this->assertFalse($writer->isClientExtensionCheckEnabled());
  }
}