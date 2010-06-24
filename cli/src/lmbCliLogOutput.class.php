<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/cli/src/lmbCliBaseOutput.class.php');
/**
 * class lmbCliLogOutput.
 * @deprecated
 * @package cli
 * @version $Id$
 */
class lmbCliLogOutput extends lmbCliBaseOutput
{
  /**
   * @var lmbLog
   */
  protected $log;

  function __construct(lmbLog $log)
  {
    $this->log = $log;
  }

  function write($message, $params = array(), $level = LOG_INFO)
  {
    $this->_log($message, $params, $level);
  }

  function error($message, $params = array(), $level = LOG_ERR)
  {
    $this->_log($message, $params, $level);
  }

  function exception(lmbException $exception)
  {
    $this->log->logException($exception);
  }

  protected function _log($message, $params, $level)
  {
    lmb_assert_type($message, 'string');
    lmb_assert_type($params, 'array');
    lmb_assert_type($level, 'integer');
    $this->log->log($message, $level, $params);
  }
}