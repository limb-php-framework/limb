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

  function testLog()
  {
    $this->log->log('imessage', LOG_INFO, 'iparam', 'ibacktrace');
    $this->assertTrue($this->_getLastLogEntry()->isLevel(LOG_INFO));
    $this->assertEqual('imessage', $this->_getLastLogEntry()->getMessage());
    $this->assertEqual('iparam', $this->_getLastLogEntry()->getParams());
    $this->assertEqual('ibacktrace', $this->_getLastLogEntry()->getBacktrace());
  }

  function testLogException()
  {
    $this->log->logException(new lmbException('exmessage', $code = 42));

    $entry = current($this->log->getWriters())->getWritten();

    $this->assertTrue($entry->isLevel(LOG_ERR));
    $this->assertEqual('exmessage', $entry->getMessage());
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
    $this->log->setBacktraceDepth(LOG_NOTICE, $depth = 0);
    $this->log->log('info', LOG_INFO);
    $this->assertNotEqual($depth, count($this->_getLastLogEntry()->getBacktrace()->get()));
    $this->log->log('notice', LOG_NOTICE);
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

    function __construct(lmbUri $dsn) {}

    function write(lmbLogEntry $entry)
    {
        $this->entry = $entry;
    }

    /**
     *@return lmbLogEntry
     */
    function getWritten()
    {
        return $this->entry;
    }
}