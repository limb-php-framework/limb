<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/toolkit/src/lmbAbstractTools.class.php');
lmb_require('limb/log/src/lmbLog.class.php');
lmb_require('limb/log/src/lmbLogFileWriter.class.php');

/**
 * class lmbLogTools.
 *
 * @package log
 * @version $Id$
 */
class lmbLogTools extends lmbAbstractTools
{
  protected $log;

  function setLog($log)
  {
    $this->log = $log;
  }

  function getLog()
  {
    if($this->log)
      return $this->log;

    $this->log = new lmbLog();

    if(defined('LIMB_VAR_DIR'))
      $this->log->registerWriter(new lmbLogFileWriter(LIMB_VAR_DIR . '/log/'));

    return $this->log;
  }
}

