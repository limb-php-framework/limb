<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/log/toolkit.inc.php');
lmb_require('limb/log/src/lmbLogEntry.class.php');

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

    $_SERVER['REQUEST_URI'] = null;
    $_SERVER['REQUEST_METHOD'] = null;
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

  function testGetLogEntryTitle_WithRequestUri()
  {
    $_SERVER['REQUEST_URI'] = '/alfa/bravo';

    $entry_title = $this->toolkit->getLogEntryTitle();

    $this->assertPattern('/\/alfa\/bravo/', $entry_title);
  }

  function testGetLogEntryTitle_WithRequestMethod()
  {
    $_SERVER['REQUEST_URI'] = '/alfa/bravo';
    $_SERVER['REQUEST_METHOD'] = 'POST';

    $entry_title = $this->toolkit->getLogEntryTitle();

    $this->assertPattern('/POST/', $entry_title);
  }


  function testGetLogEntryTitle_setTitle()
  {
    $_SERVER['REQUEST_URI'] = '/alfa/bravo';
    $_SERVER['REQUEST_METHOD'] = 'POST';

    $this->toolkit->setLogEntryTitle('entry title');

    $entry = new lmbLogEntry(LOG_INFO, 'message');
    $entry_title = $this->toolkit->getLogEntryTitle($entry);

    $this->assertEqual($entry_title, 'entry title');
  }

  function testLog_DefaultEntryTitle()
  {
    $_SERVER['REQUEST_URI'] = '/alfa/bravo';
    $_SERVER['REQUEST_METHOD'] = 'POST';

    $log_file_path = 'file://' . lmb_var_dir() . 'testLog_setTitle';
    $conf = array(
      'logs' => array('foo' => $log_file_path)
    );

    $this->toolkit->setConf('log', $conf);

    $this->toolkit->log($message = 'message', $level = LOG_INFO, $params = array(), $backtrace = null, $log_name = 'foo');

    $log_file_content = file_get_contents($log_file_path);
    $this->assertPattern('/\/alfa\/bravo/', $log_file_content);
  }

  function testLog_setEntryTitle()
  {
    $_SERVER['REQUEST_URI'] = '/alfa/bravo';
    $_SERVER['REQUEST_METHOD'] = 'POST';

    $log_file_path = 'file://' . lmb_var_dir() . 'testLog_setTitle';
    $conf = array(
      'logs' => array('foo' => $log_file_path)
    );

    $this->toolkit->setConf('log', $conf);
    $this->toolkit->setLogEntryTitle('entry title');

    $this->toolkit->log($message = 'message', $level = LOG_INFO, $params = array(), $backtrace = null, $log_name = 'foo');

    $log_file_content = file_get_contents($log_file_path);
    $this->assertPattern('/entry title/', $log_file_content);
  }

  function testLog_testDefaultLogName()
  {
    $log_file_path_1 = 'file://' . lmb_var_dir() . 'testLog_setTitle_1';
    $log_file_path_2 = 'file://' . lmb_var_dir() . 'testLog_setTitle_2';

    $conf = array(
      'logs' => array(
        'foo' => $log_file_path_1,
        'default' => $log_file_path_2
      )
    );

    $this->toolkit->setConf('log', $conf);
    $this->toolkit->setLogEntryTitle('entry title');

    $this->toolkit->log($message = 'message');

    $log_file_content = file_get_contents($log_file_path_2);
    $this->assertPattern('/message/', $log_file_content);
  }

  function testLogException_defaultEntryTitle()
  {
    $_SERVER['REQUEST_URI'] = '/alfa/bravo';
    $_SERVER['REQUEST_METHOD'] = 'POST';

    $log_file_path = 'file://' . lmb_var_dir() . 'testLogException_defaultEntryTitle';
    $conf = array(
      'logs' => array('foo' => $log_file_path)
    );

    $this->toolkit->setConf('log', $conf);

    $this->toolkit->logException(new lmbInvalidArgumentException('exmessage', $code = 42), $log_name = 'foo');

    $log_file_content = file_get_contents($log_file_path);

    $this->assertPattern('/\/alfa\/bravo/', $log_file_content);
    $this->assertPattern('/POST/', $log_file_content);
  }

  function testLogException_setEntryTitle()
  {
    $_SERVER['REQUEST_URI'] = '/alfa/bravo';
    $_SERVER['REQUEST_METHOD'] = 'POST';

    $log_file_path = 'file://' . lmb_var_dir() . 'testLogException_setEntryTitle';
    $conf = array(
      'logs' => array('foo' => $log_file_path)
    );

    $this->toolkit->setConf('log', $conf);
    $this->toolkit->setLogEntryTitle('entry title');

    $this->toolkit->logException(new lmbInvalidArgumentException('exmessage', $code = 42), $log_name = 'foo');

    $log_file_content = file_get_contents($log_file_path);
    $this->assertPattern('/entry title/', $log_file_content);
  }

  function testLogException_testDefaultLogName()
  {
    $log_file_path_1 = 'file://' . lmb_var_dir() . 'testLogException_testDefaultLogName_1';
    $log_file_path_2 = 'file://' . lmb_var_dir() . 'testLogException_testDefaultLogName_2';

    $conf = array(
      'logs' => array(
        'foo' => $log_file_path_1,
        'default' => $log_file_path_2
      )
    );

    $this->toolkit->setConf('log', $conf);
    $this->toolkit->setLogEntryTitle('entry title');

    $this->toolkit->logException(new lmbInvalidArgumentException('exmessage', $code = 42));

    $log_file_content = file_get_contents($log_file_path_2);

    $this->assertPattern('/exmessage/', $log_file_content);
  }
}

