<?php
/**
 * class lmbLogBaseWriter.
 *
 * @package log
 * @version $Id$
 */
lmb_require('limb/log/src/lmbLogWriter.interface.php');

abstract class lmbLogBaseWriter implements lmbLogWriter
{

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
      return $entry;
  }

  /**
   * @param lmbLogEntry $entry
   * @return string
   */
  protected function _getDelimiter($entry)
  {
    $time = strftime("%b %d %Y %H:%M:%S", $entry->getTime());
    $delimiter_len = (int) ((80 - strlen($time)) / 2) - 2;
    return str_repeat('=', $delimiter_len)."[{$time}]".str_repeat('=', $delimiter_len);
  }

  /**
   * @param lmbLogEntry $entry
   * @return string
   */
  protected function _getDefaultEntryString($entry)
  {
    $log_message = $entry->getLevelForHuman().' '.$entry->getMessage().PHP_EOL;
    if($entry->getParams())
      $log_message .= 'Additional params: '.strstr(print_r($entry->getParams(), true), PHP_EOL);
    $log_message .= 'Backtrace: '.PHP_EOL.$entry->getBacktrace()->toString();
    return $log_message;
  }
}