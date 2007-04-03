<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbLoggingErrorDispatcher.class.php 4995 2007-02-08 15:36:14Z pachanga $
 * @package    error
 */
lmb_require('limb/error/src/error/lmbErrorDispatcher.class.php');
lmb_require('limb/util/src/util/lmbLog.class.php');

/**
 * Logging dispatcher
 */
class lmbLoggingErrorDispatcher extends lmbErrorDispatcher
{
  protected $loggingRules = array();
  protected $errorLogging = true;
  protected $exceptionLogging = true;
  protected $exceptionLogFileName;

  function exceptionDispatch($exception)
  {
    $this->logException($exception->getMessage());
  }

  function errorDispatch($errorNumber, $errorMessage, $fileName, $lineNumber, $arguments)
  {
    $message = "{$fileName}:{$lineNumber}\nInterpretator says: {$errorMessage}";
    $this->logError($errorNumber, $message);
  }

  function logError($errorNumber, $message)
  {
    foreach ( $this->loggingRules as $errorMask => $fileName )
    {
        if ( ($errorNumber & $errorMask) != 0 )
        {
            lmbLog :: write($fileName, $message."\n");
            return true;
        }
    }
    return false;
  }

  function logException($message)
  {
    lmbLog :: write($this->exceptionLogFileName, $message."\n");
    return true;
  }

  function addErrorLoggingRule($errorLevel, $fileName)
  {
    $this->loggingRules[$errorLevel] = $fileName;
  }

  function setExceptionLogFileName($fileName)
  {
    $this->exceptionLogFileName = $fileName;
  }

  function errorLogging($status = NULL)
  {
    $oldStatus = $this->errorLogging;
    if ( $status !== NULL )
    {
      $this->errorLogging = (bool)$status;
    }
    return $oldStatus;
  }

  function exceptionLogging($status = NULL)
  {
    $oldStatus = $this->exceptionLogging;
    if ( $status !== NULL )
    {
      $this->exceptionLogging = (bool)$status;
    }
    return $oldStatus;
  }
}
?>