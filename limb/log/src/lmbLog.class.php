<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/log/src/lmbLogEntry.class.php');
lmb_require('limb/core/src/lmbBacktrace.class.php');

/**
 * class lmbLog.
 *
 * @package log
 * @version $Id$
 */
class lmbLog
{
  const NOTICE  = 1;
  const WARNING = 2;
  const ERROR   = 3;
  const INFO    = 4;

  protected $logs = array();
  protected $log_writers = array();
  protected $allowed_levels = array();
  protected $backtrace_depth = array(
    'notice' => 1,
    'warning' => 1,
    'info' => 3,
    'error' => 5,
    'exception' => 5
  );

  function __construct()
  {
    $this->allowed_levels = array(
      lmbLog :: NOTICE => true,
      lmbLog :: WARNING => true,
      lmbLog :: ERROR => true,
      lmbLog :: INFO => true
    );
  }

  function skipNotice()
  {
    $this->_skipErrorLevel(lmbLog :: NOTICE);
  }

  function skipWarning()
  {
    $this->_skipErrorLevel(lmbLog :: WARNING);
  }

  function skipError()
  {
    $this->_skipErrorLevel(lmbLog :: ERROR);
  }

  function skipInfo()
  {
    $this->_skipErrorLevel(lmbLog :: INFO);
  }

  function _skipErrorLevel($level)
  {
    $this->allowed_levels[$level] = false;
  }

  function registerWriter($writer)
  {
    $this->log_writers[] = $writer;
  }

  function reset()
  {
    $this->logs = array();
  }

  function getLogs()
  {
    return $this->logs;
  }

  function sizeof()
  {
    return sizeof($this->logs);
  }

  function setBacktraceDepth($log_level, $depth) {
    $this->backtrace_depth[$log_level] = $depth;
  }

  function notice($message, $params = array(), $backtrace = null)
  {
    if(!$this->isLogEnabled())
      return;

    if(!$backtrace)
      $backtrace = new lmbBacktrace($this->backtrace_depth['notice']);

    $this->_write(lmbLog :: NOTICE, $message, $params, $backtrace);
  }

  function warning($message, $params = array(), $backtrace = null)
  {
    if(!$this->isLogEnabled())
      return;

    if(!$backtrace)
      $backtrace = new lmbBacktrace($this->backtrace_depth['warning']);

    $this->_write(lmbLog :: WARNING, $message, $params, $backtrace);
  }

  function error($message, $params = array(), $backtrace = null)
  {
    if(!$this->isLogEnabled())
      return;

    if(!$backtrace)
      $backtrace = new lmbBacktrace($this->backtrace_depth['error']);

    $this->_write(lmbLog :: ERROR, $message, $params, $backtrace);
  }

  function exception($e)
  {
    if(!$this->isLogEnabled())
      return;

    if($e instanceof lmbException)
      $this->error($e->getMessage(), $e->getParams(), new lmbBacktrace($e->getTrace(), $this->backtrace_depth['exception']));
    else
      $this->error($e->getMessage(), array(), new lmbBacktrace($e->getTrace(), $this->backtrace_depth['exception']));
  }

  function info($message, $params = array(), $backtrace = null)
  {
    if(!$this->isLogEnabled())
      return;

    if(!$backtrace)
      $backtrace = new lmbBacktrace($this->backtrace_depth['info']);

    $this->_write(lmbLog :: INFO, $message, $params, $backtrace);
  }

  protected function _write($level, $string, $params = array(), $backtrace = null)
  {
    if(!$this->_isAllowedLevel($level))
      return;

    $entry = new lmbLogEntry($level, $string, $params, $backtrace);
    $this->logs[] = $entry;

    $this->_writeLogEntry($entry);
  }

  function _isAllowedLevel($level)
  {
    return isset($this->allowed_levels[$level]) && $this->allowed_levels[$level];
  }

  function _writeLogEntry($entry)
  {
    foreach($this->log_writers as $writer)
      $writer->write($entry);
  }

  function isLogEnabled()
  {
    return (bool) lmb_env_get('LIMB_LOG_ENABLE', true);
  }
}


