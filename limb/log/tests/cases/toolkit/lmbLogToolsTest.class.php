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
  /**
   * @var lmbLogTools
   */
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

  function testCreateLogWriterByDSN()
  {
    $writer = $this->toolkit->createLogWriterByDSN('file:///testCreateLogWriterByDSN');
    $this->assertIsA($writer, 'lmbLogFileWriter');
    $this->assertEqual($writer->getLogFile(), '/testCreateLogWriterByDSN');
  }

  function testCreateLog_ByWriters()
  {
    $log = $this->toolkit->createLog(array(
      new lmbLogFileWriter(new lmbUri('file:///a')),
      new lmbLogEchoWriter(new lmbUri('echo:/')),
    ));
    $writers = $log->getWriters();
    $this->assertIsA($writers[0], 'lmbLogFileWriter');
    $this->assertEqual($writers[0]->getLogFile(), '/a');
    $this->assertIsA($writers[1], 'lmbLogEchoWriter');
  }

  function testCreateLog_ByDSNes()
  {
    $log = $this->toolkit->createLog(array(
      new lmbUri('file:///a'),
      new lmbUri('echo:/'),
    ));
    $writers = $log->getWriters();
    $this->assertIsA($writers[0], 'lmbLogFileWriter');
    $this->assertEqual($writers[0]->getLogFile(), '/a');
    $this->assertIsA($writers[1], 'lmbLogEchoWriter');
  }

  function testCreateLog_ByDSNesStrings()
  {
    $log = $this->toolkit->createLog(array(
      'file:///a',
      'echo:/',
    ));
    $writers = $log->getWriters();
    $this->assertIsA($writers[0], 'lmbLogFileWriter');
    $this->assertEqual($writers[0]->getLogFile(), '/a');
    $this->assertIsA($writers[1], 'lmbLogEchoWriter');
  }

  function testGetLog_Default()
  {
    $writers = $this->toolkit->getLog()->getWriters();
    $this->assertIsA($writers[0], 'lmbLogFileWriter');
    $this->assertEqual($writers[0]->getLogFile(), lmb_var_dir().'/logs/error.log');
  }

  function testSetAndGetLog()
  {
    $log = $this->toolkit->createLog(array('file:///testSetAndGetLog'));
    $this->toolkit->setLog($log);
    $writers = $this->toolkit->getLog()->getWriters();
    $this->assertIsA($writers[0], 'lmbLogFileWriter');
    $this->assertEqual($writers[0]->getLogFile(), '/testSetAndGetLog');
  }

  function testSetAndGetLog_ByName()
  {
    $log = $this->toolkit->createLog(array('file:///testSetAndGetLog'));
    $this->toolkit->setLog($log, 'custom_log');
    $writers = $this->toolkit->getLog('custom_log')->getWriters();
    $this->assertIsA($writers[0], 'lmbLogFileWriter');
    $this->assertEqual($writers[0]->getLogFile(), '/testSetAndGetLog');
  }

  function testGetLog_fromConf()
  {
    $conf = array(
      'logs' => array('foo' => 'file:///testCreateLogByName')
    );
    $this->toolkit->setConf('log', $conf);
    $log = $this->toolkit->getLog('foo');
    $writers = $log->getWriters();
    $this->assertIsA($writers[0], 'lmbLogFileWriter');
    $this->assertEqual($writers[0]->getLogFile(), '/testCreateLogByName');
  }

  function testGetLog_fromConf_MultipleWriters()
  {
    $conf = array(
      'logs' => array(
        'foo' => array(
          'file:///testCreateLogByName_MultipleWriters',
          'file:///testCreateLogByName_MultipleWriters2'
        )
      )
    );
    $this->toolkit->setConf('log', $conf);

    $log = $this->toolkit->getLog('foo');
    $writers = $log->getWriters();
    $this->assertIsA($writers[0], 'lmbLogFileWriter');
    $this->assertEqual($writers[0]->getLogFile(), '/testCreateLogByName_MultipleWriters');
    $this->assertIsA($writers[1], 'lmbLogFileWriter');
    $this->assertEqual($writers[1]->getLogFile(), '/testCreateLogByName_MultipleWriters2');
  }

  function testGetLogByName()
  {
    $conf = array(
      'logs' => array('foo' => 'file:///testGetLogByName')
    );
    $this->toolkit->setConf('log', $conf);
    $log_writers = $this->toolkit->getLog('foo')->getWriters();
    $this->assertIsA($log_writers[0], 'lmbLogFileWriter');
    $this->assertEqual($log_writers[0]->getLogFile(), '/testGetLogByName');
  }

  function testGetLogFromConf()
  {
    $conf = array(
      'logs' => array('foo' => 'file:///testGetLogFromConf')
    );
    $this->toolkit->setConf('log', $conf);
    $log = $this->toolkit->getLogFromConf('foo');
    
    $this->assertIsA($log, 'lmbLog');
    $writers = $log->getWriters();
    $this->assertIsA($writers[0], 'lmbLogFileWriter');
    $this->assertEqual($writers[0]->getLogFile(), '/testGetLogFromConf');
  }
}