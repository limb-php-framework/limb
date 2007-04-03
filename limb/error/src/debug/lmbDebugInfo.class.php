<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbDebugInfo.class.php 4995 2007-02-08 15:36:14Z pachanga $
 * @package    error
 */
define('LIMB_DEBUG_LEVEL_NOTICE',   1);
define('LIMB_DEBUG_LEVEL_WARNING',  2);
define('LIMB_DEBUG_LEVEL_ERROR',    3);
define('LIMB_DEBUG_LEVEL_INFO',     4);

class lmbDebugInfo
{
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
      case LIMB_DEBUG_LEVEL_NOTICE:
        $level = 'notice';
      break;

      case LIMB_DEBUG_LEVEL_WARNING:
        $level = 'warning';
      break;

      case LIMB_DEBUG_LEVEL_ERROR:
        $level = 'error';
      break;

      case LIMB_DEBUG_LEVEL_INFO:
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
