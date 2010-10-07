<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/log/src/lmbLog.class.php');
lmb_require('limb/log/src/lmbLogWriter.interface.php');

class lmbLogTest extends UnitTestCase {

  /**
   * @var lmbLog
   */
  protected $log;

  function setUp()
  {
    $this->log = new lmbLog();
    $this->log->registerWriter(new lmbLogWriterForLogTests(new lmbUri()));
  }

  function testWritersManipulation()
  {
    $log = new lmbLog();
    $this->assertEqual(array(), $log->getWriters());

    $log->registerWriter($writer = new lmbLogWriterForLogTests(new lmbUri()));
    $this->assertEqual(array($writer), $log->getWriters());

    $log->resetWriters();
    $this->assertEqual(array(), $log->getWriters());
  }

  function testAddWritersByConstruct()
  {
    $writer1 = new lmbLogWriterForLogTests(new lmbUri());
    $writer2 = new lmbLogWriterForLogTests(new lmbUri());

    $log = new lmbLog(array($writer1, $writer2));
    $writers = $log->getWriters();

    $this->assertIdentical($writer1, $writers[0]);
    $this->assertIdentical($writer2, $writers[1]);
  }

  function testLog()
  {
    $this->log->log('imessage', LOG_INFO, 'iparam', $backtrace = new lmbBacktrace(1), $entry_title='title');
    $this->assertTrue($this->_getLastLogEntry()->isLevel(LOG_INFO));
    $this->assertEqual('imessage', $this->_getLastLogEntry()->getMessage());
    $this->assertEqual('title', $this->_getLastLogEntry()->getTitle());
    $this->assertEqual('iparam', $this->_getLastLogEntry()->getParams());
    $this->assertEqual($backtrace, $this->_getLastLogEntry()->getBacktrace());
  }

  function testLogException()
  {
    $this->log->logException(new lmbInvalidArgumentException('exmessage', $code = 42));

    $entry = current($this->log->getWriters())->getWritten();

    $this->assertTrue($entry->isLevel(LOG_ERR));
    $this->assertEqual('lmbInvalidArgumentException: exmessage', $entry->getMessage());
  }

  function testSetErrorLevel()
  {
    $this->log->setErrorLevel(LOG_WARNING);
    $this->log->log('info', LOG_INFO);
    $this->log->log('notice', LOG_NOTICE);
    $this->assertNull($this->_getLastLogEntry());
  }

  function testSetBacktraceDepth()
  {
    $this->log->setBacktraceDepth(LOG_ERR, $depth = 0);
    $this->log->log('notice', LOG_NOTICE);
    $this->assertNotEqual($depth, count($this->_getLastLogEntry()->getBacktrace()->get()));
    $this->log->log('error', LOG_ERR);
    $this->assertEqual($depth, count($this->_getLastLogEntry()->getBacktrace()->get()));
  }

  /**
   *@return lmbLogEntry
   */
  protected function _getLastLogEntry()
  {
    return current($this->log->getWriters())->getWritten();
  }
}

class lmbLogWriterForLogTests implements lmbLogWriter {

  protected $entry;
  protected $_log_level;
  protected $_dsn;

  /**
   * @param lmbUri $dsn
   */
  function __construct(lmbUri $dsn)
  {
    $this->_dsn = $dsn;
    $this->_log_level = ($level = $this->_dsn->getQueryItem('level')) !== false ? $level : LOG_INFO;
  }

  /**
   * @param int $level
   */
  function setErrorLevel($level)
  {
    $this->_log_level = $level;
  }

  /**
   * @param lmbLogEntry $entry
   * @return boolean
   */
  function isAllowedLevel(lmbLogEntry $entry)
  {
    return $entry->getLevel() <= $this->_log_level;
  }

  /**
   * @param lmbLogEntry $entry
   * @return boolean
   */
  function write(lmbLogEntry $entry)
  {
    if($this->isAllowedLevel($entry))
      return $this->_write($entry);
  }

  /**
   * @param lmbLogEntry $entry
   * @return boolean
   */
  protected function _write(lmbLogEntry $entry)
  {
    return $this->entry = $entry;
  }

  /**
   *@return lmbLogEntry
   */
  function getWritten()
  {
      return $this->entry;
  }
}