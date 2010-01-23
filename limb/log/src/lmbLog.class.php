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
    LOG_NOTICE  => 1,
    LOG_WARNING => 1,
    LOG_INFO    => 3,
    LOG_ERR     => 5
  );

  function registerWriter($writer)
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

  function isLogEnabled()
  {
    return (bool) lmb_env_get('LIMB_LOG_ENABLE', true);
  }

  function setErrorLevel($level)
  {
    $this->level = $level;
  }

  function setBacktraceDepth($log_level, $depth) {
    $this->backtrace_depth[$log_level] = $depth;
  }

  function log($message, $level = LOG_INFO, $params = array(), $backtrace = null)
  {
    if(!$this->isLogEnabled())
      return;

    if(!$backtrace)
      $backtrace = new lmbBacktrace($this->backtrace_depth[$level]);

    $this->_write($level, $message, $params, $backtrace);
  }

  function logException($exception)
  {
    if(!$this->isLogEnabled())
      return;

    $backtrace_depth = $this->backtrace_depth[LOG_ERR];

    if($exception instanceof lmbException)
      $this->log(
        $exception->getMessage(),
        LOG_ERR,
        $exception->getParams(),
        new lmbBacktrace($exception->getTrace(), $backtrace_depth)
      );
    else
      $this->log(
        $exception->getMessage(),
        LOG_ERR,
        array(),
        new lmbBacktrace($exception->getTrace(), $backtrace_depth)
      );
  }

  protected function _write($level, $string, $params = array(), $backtrace = null)
  {
    if(!$this->_isAllowedLevel($level))
      return;

    $entry = new lmbLogEntry($level, $string, $params, $backtrace);
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
