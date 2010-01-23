<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

lmb_require('limb/core/src/lmbSys.class.php');

/**
 * class lmbLogEntry.
 *
 * @package log
 * @version $Id$
 */
class lmbLogEntry
{
  protected $level;
  protected $time;
  protected $message;
  protected $params;
  protected $backtrace;
  protected $names_map = array(
    LOG_INFO    => 'Info',
    LOG_NOTICE  => 'Notice',
    LOG_WARNING => 'Warning',
    LOG_ERR     => 'Error',
  );

  function __construct($level, $message, $params = array(), $backtrace = null, $time = null)
  {
    $this->level = $level;
    $this->message = $message;
    $this->params = $params;
    $this->backtrace = $backtrace;
    $this->time = !$time ? time() : $time;
  }

  function getLevel()
  {
    return $this->level;
  }

  function getMessage()
  {
    return $this->message;
  }

  function getTime()
  {
    return $this->time;
  }

  function getParams()
  {
    return $this->params;
  }

  function getBacktrace()
  {
    return $this->backtrace;
  }

  function isLevel($level)
  {
    return $this->level == $level;
  }

  function getLevelForHuman()
  {
    return $this->names_map[$this->level];
  }

  function toString()
  {
    return lmbSys :: isCli() ? $this->asText() : $this->asHtml();
  }

  function asText()
  {
    $string = $this->getLevelForHuman()." message: {$this->message}";
    $string .= (count($this->params) ? "\nAdditional attributes: " . var_export($this->params, true) : '');
    if($this->backtrace)
      $string .= "\nBack trace:\n" . $this->backtrace->toString();

    return $string;
  }

  function asHtml()
  {
    return '<pre>' . htmlspecialchars($this->asText()) . '</pre>';
  }
}


