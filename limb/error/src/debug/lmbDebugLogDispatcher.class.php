<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbDebugLogDispatcher.class.php 4995 2007-02-08 15:36:14Z pachanga $
 * @package    error
 */

class lmbDebugLogDispatcher
{
  protected $log_files;

  function __construct()
  {
    $this->log_files = array(
      LIMB_DEBUG_LEVEL_NOTICE => array(LIMB_VAR_DIR . '/log/notice.log', true),
      LIMB_DEBUG_LEVEL_WARNING => array(LIMB_VAR_DIR . '/log/warning.log', true),
      LIMB_DEBUG_LEVEL_ERROR => array(LIMB_VAR_DIR . '/log/error.log', true),
      LIMB_DEBUG_LEVEL_INFO => array(LIMB_VAR_DIR . '/log/debug.log', true)
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
