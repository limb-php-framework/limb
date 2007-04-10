<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbDebugLogDispatcher.class.php 5602 2007-04-10 10:04:28Z pachanga $
 * @package    error
 */
lmb_require(dirname(__FILE__) . '/lmbDebugInfo.class.php');

class lmbDebugLogDispatcher
{
  protected $log_files;

  function __construct($log_dir)
  {
    $this->log_files = array(
      lmbDebugInfo :: NOTICE => array($log_dir . '/notice.log', true),
      lmbDebugInfo :: WARNING => array($log_dir . '/warning.log', true),
      lmbDebugInfo :: ERROR => array($log_dir . '/error.log', true),
      lmbDebugInfo :: INFO => array($log_dir . '/debug.log', true)
    );
  }

  function dispatch($debug_info)
  {
    lmb_require('limb/util/src/util/lmbLog.class.php');

    lmbLog :: write($this->getLogFile($debug_info->getLevel()), $debug_info->asText());
    error_log($debug_info->getLevelForHuman() . ' : ' . $debug_info->getMessage());
  }

  function getLogFile($level)
  {
    if(isset($this->log_files[$level]) &&
       $this->log_files[$level][1])
      return $this->log_files[$level][0];

    return false;
  }
}

?>
