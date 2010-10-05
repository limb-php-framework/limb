<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/log/src/lmbLogWriter.interface.php');
lmb_require('limb/log/src/lmbLogEntry.class.php');
lmb_require('limb/core/src/lmbBacktrace.class.php');
lmb_require('limb/net/src/lmbUri.class.php');
lmb_require('limb/fs/src/exception/lmbFileNotFoundException.class.php');

/**
 * class lmbLog.
 *
 * @package log
 * @version $Id$
 */
class lmbLog
{
  protected $logs = array();
  protected $log_writers = array();
  protected $level = PHP_INT_MAX;

  protected $backtrace_depth = array(
    LOG_ERR     => 5,
    LOG_WARNING => 3,
    LOG_NOTICE  => 3,
    LOG_INFO    => 3,
    LOG_DEBUG   => 3,
  );

  function __construct(array $writers = array())
  {
    foreach ($writers as $writer)
      $this->registerWriter($writer);
  }

  function registerWriter(lmbLogWriter $writer)
  {
    $this->log_writers[] = $writer;
  }

  function getWriters()
  {
    return $this->log_writers;
  }

  function resetWriters()
  {
    $this->log_writers = array();
  }

  function setErrorLevel($level)
  {
    $this->level = $level;
    $this->_setWriersErrorLevel($level);
  }

  protected function _setWriersErrorLevel($level)
  {
    foreach($this->log_writers as $writer)
      $writer->setErrorLevel($level);
  }

  function setBacktraceDepth($log_level, $depth) {
    $this->backtrace_depth[$log_level] = $depth;
  }

  function log($message, $level = LOG_INFO, $params = array(), lmbBacktrace $backtrace = null, $entry_title = null)
  {
    lmb_assert_type($level, 'integer');

    if(!$backtrace)
    {
      lmb_assert_array_with_key($this->backtrace_depth, $level);
      $backtrace = new lmbBacktrace($this->backtrace_depth[$level]);
    }

    $this->_write($level, $message, $params, $backtrace, $entry_title);
  }

  function logException(Exception $exception, $entry_title = null)
  {
    $message = get_class($exception) . ': ';
    if($exception instanceof lmbException)
    {
      $message .= $exception->getOriginalMessage();
      $backtrace = $exception->getBacktrace();
      $params = $exception->getParams();
    }
    else
    {
      $message .= $exception->getMessage();
      $backtrace = $exception->getTrace();
      $params = null;
    }

    $backtrace_depth = $this->backtrace_depth[LOG_ERR];
    $this->log($message, LOG_ERR, $params, new lmbBacktrace($backtrace, $backtrace_depth), $entry_title);
  }

  protected function _write($level, $string, $params = array(), $backtrace = null, $entry_title = null)
  {
    $entry = new lmbLogEntry($level, $string, $params, $backtrace);

    if($entry_title)
      $entry->setTitle($entry_title);

    $this->logs[] = $entry;

    $this->_writeLogEntry($entry);
  }

  protected function _isAllowedLevel($level)
  {
    return $level <= $this->level;
  }

  protected function _writeLogEntry($entry)
  {
    foreach($this->log_writers as $writer)
      $writer->write($entry);
  }
}
