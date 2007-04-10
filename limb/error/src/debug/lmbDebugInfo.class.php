<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbDebugInfo.class.php 5602 2007-04-10 10:04:28Z pachanga $
 * @package    error
 */

class lmbDebugInfo
{
  const NOTICE  = 1;
  const WARNING = 2;
  const ERROR   = 3;
  const INFO    = 4;

  protected $level;
  protected $time;
  protected $message;
  protected $params;
  protected $backtrace;

  function __construct($level, $message, $params = array(), $backtrace = null, $time = null)
  {
    $this->level = $level;
    $this->message = $message;
    $this->params = $params;
    $this->backtrace = !$backtrace ? new lmbBacktrace() : $backtrace;
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

  function isLevel($level)
  {
    return $this->level == $level;
  }

  function getLevelForHuman()
  {
    $level = '';

    switch($this->level)
    {
      case lmbDebugInfo :: NOTICE:
        $level = 'notice';
      break;

      case lmbDebugInfo :: WARNING:
        $level = 'warning';
      break;

      case lmbDebugInfo :: ERROR:
        $level = 'error';
      break;

      case lmbDebugInfo :: INFO:
        $level = 'info';
      break;
    }
    return $level;
  }

  function toString()
  {
    return lmbSys :: isCli() ? $this->asText() : $this->asHtml();
  }

  function asText()
  {
    $string = "Message: {$this->message}";
    $string .= (count($this->params) ? "\nAdditional attributes: " . var_export($this->params, true) : '');
    $string .= "\nBack trace:\n" . $this->backtrace->toString();

    return $string;
  }

  function asHtml()
  {
    return '<pre>' . htmlspecialchars($this->asText()) . '</pre>';
  }
}

?>
