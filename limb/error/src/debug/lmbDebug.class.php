<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbDebug.class.php 5602 2007-04-10 10:04:28Z pachanga $
 * @package    error
 */
@define('LIMB_DEBUG_LOG', true);
@define('LIMB_DEBUG_SHOW', false);

lmb_require(dirname(__FILE__) . '/lmbDebugInfo.class.php');
lmb_require(dirname(__FILE__) . '/lmbBacktrace.class.php');
lmb_require(dirname(__FILE__) . '/lmbStopwatch.class.php');

class lmbDebug
{
  static protected $instance;
  protected $debug_info = array();
  protected $debug_dispatchers = array();
  protected $allowed_levels = array();
  protected $mock;

  function __construct()
  {
    $this->allowed_levels = array(
      lmbDebugInfo :: NOTICE => true,
      lmbDebugInfo :: WARNING => true,
      lmbDebugInfo :: ERROR => true,
      lmbDebugInfo :: INFO => true
    );
  }

  static function instance()
  {
    if(!self :: $instance)
      self :: $instance = new lmbDebug();

    return self :: $instance;
  }

  function skipNotice()
  {
    $this->_skipErrorLevel(lmbDebugInfo :: NOTICE);
  }

  function skipWarning()
  {
    $this->_skipErrorLevel(lmbDebugInfo :: WARNING);
  }

  function skipError()
  {
    $this->_skipErrorLevel(lmbDebugInfo :: ERROR);
  }

  function skipInfo()
  {
    $this->_skipErrorLevel(lmbDebugInfo :: INFO);
  }

  function _skipErrorLevel($level)
  {
    $this->allowed_levels[$level] = false;
  }

  static function registerDispatcher($dispatcher)
  {
    self :: instance()->debug_dispatchers[] = $dispatcher;
  }

  function reset()
  {
    $this->debug_info = array();
  }

  static function getInfo()
  {
    return self :: instance()->debug_info;
  }

  static function sizeof()
  {
    return sizeof(self :: instance()->debug_info);
  }

  function notice($message, $params = array(), $backtrace = null)
  {
    if(!$backtrace)
      $backtrace = new lmbBacktrace(null, 3);

    self :: instance()->register(lmbDebugInfo :: NOTICE, $message, $params, $backtrace);
  }

  function warning($message, $params = array(), $backtrace = null)
  {
    if(!$backtrace)
      $backtrace = new lmbBacktrace(null, 3);

    self :: instance()->register(lmbDebugInfo :: WARNING, $message, $params, $backtrace);
  }

  function error($message, $params = array(), $backtrace = null)
  {
    if(!$backtrace)
      $backtrace = new lmbBacktrace(null, 5);

    self :: instance()->register(lmbDebugInfo :: ERROR, $message, $params, $backtrace);
  }

  function exception($e)
  {
    if(is_a($e, 'lmbException'))
      self :: error($e->getMessage(), $e->getParams(), new lmbBacktrace($e->getTrace()));
    else
      self :: error($e->getMessage(), array(), new lmbBacktrace($e->getTrace()));
  }

  function info($message, $params = array(), $backtrace = null)
  {
    if(!$backtrace)
      $backtrace = new backtrace(3);

    self :: instance()->register(lmbDebugInfo :: INFO, $message, $params, $backtrace);
  }

  //shouldn't be called directly
  protected function register($verbosity_level, $string, $params = array(), $backtrace = null)
  {
    if(!$this->isDebugEnabled())
      return;

    if($this->_expectationCatched($verbosity_level, $string, $params))
      return;

    if(!$this->_isAllowedLevel($verbosity_level))
      return;

    if(isset($GLOBALS['DEBUG_RECURSION']) && $GLOBALS['DEBUG_RECURSION'])
    {
      echo($string . ' - (debug recursion!!!)');
      exit(1);
    }

    $GLOBALS['DEBUG_RECURSION'] = 1;

    $debug_info = $this->_addDebugInfo($verbosity_level, $string, $params, $backtrace);

    $this->_dispatchDebugInfo($debug_info);

    unset($GLOBALS['DEBUG_RECURSION']);
  }

  function _isAllowedLevel($level)
  {
    return isset($this->allowed_levels[$level]) && $this->allowed_levels[$level];
  }

  function _addDebugInfo($verbosity_level, $string, $params, $backtrace)
  {
    $debug_info = new lmbDebugInfo($verbosity_level, $string, $params, $backtrace);
    $this->debug_info[] = $debug_info;
    return $debug_info;
  }

  function _dispatchDebugInfo($debug_info)
  {
    foreach(array_keys($this->debug_dispatchers) as $key)
      $this->debug_dispatchers[$key]->dispatch($debug_info);
  }

  function resetExpectations()
  {
    $this->mock = null;
  }

  function expectNotice($message, $params = array())
  {
    self :: instance()->expectRegister(lmbDebugInfo :: NOTICE, $message, $params);
  }

  function expectWarning($message, $params = array())
  {
    self :: instance()->expectRegister(lmbDebugInfo :: WARNING, $message, $params);
  }

  function expectError($message, $params = array())
  {
    self :: instance()->expectRegister(lmbDebugInfo :: ERROR, $message, $params);
  }

  function expectRegister($verbosity_level, $message, $params)
  {
    $this->_ensureMock();
    $this->mock->expectArgumentsAt($this->mock->getNextTiming(), 'register', array($verbosity_level, $message, $params, MOCK_ANYTHING));
    $this->mock->setReturnValueAt($this->mock->getNextTiming(), 'register', true, array($verbosity_level, $message, $params, MOCK_ANYTHING));
  }

  function _expectationCatched($verbosity_level, $message, $params)
  {
    if(!$this->_usingMock())
      return false;

    return $this->mock->register($verbosity_level, $message, $params, null) === true;
  }

  function _usingMock()
  {
    return is_object($this->mock);
  }

  function _ensureMock()
  {
    if($this->_usingMock())
      return;

    lmb_require(dirname(__FILE__) . '/lmbDebugMock.class.php');
    $this->mock = new lmbDebugMock();
  }

  function isDebugEnabled()
  {
    return (!defined('LIMB_DEBUG_ENABLE') ||
            (defined('LIMB_DEBUG_ENABLE') && constant('LIMB_DEBUG_ENABLE')));
  }
}

if(LIMB_DEBUG_SHOW)
{
  lmb_require(dirname(__FILE__) . '/lmbDebugPrintDispatcher.class.php');
  lmbDebug :: registerDispatcher(new lmbDebugPrintDispatcher());
}

if(LIMB_DEBUG_LOG)
{
  lmb_require(dirname(__FILE__) . '/lmbDebugLogDispatcher.class.php');
  lmbDebug :: registerDispatcher(new lmbDebugLogDispatcher(LIMB_VAR_DIR . '/log'));
}

?>