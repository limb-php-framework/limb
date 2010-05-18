<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/cli/src/lmbCliOption.class.php');
/**
 * class lmbCliInput.
 *
 * @package cli
 * @version $Id: lmbCliInput.class.php 7686 2009-03-04 19:57:12Z korchasa $
 */
class lmbCliInput
{
  protected $minimum_args = null;
  protected $options = array();
  protected $arguments = array();
  protected $throw_exception = false;
  protected $strict_mode = true;
  protected $argv;

  function __construct()
  {
  }

  function read($argv = null, $is_posix = true)
  {
    try
    {
      if(is_null($argv))
        $this->argv = self :: readPHPArgv();
      else
        $this->argv = $argv;

      if($is_posix)
        array_shift($this->argv);

      $this->_parse($this->argv);
    }
    catch(lmbCliExceptio $e)
    {
      if($this->throw_exception)
        throw $e;

      return false;
    }
    return true;
  }

  function getArgv()
  {
    return $this->argv;
  }

  function throwException($flag = true)
  {
    $this->throw_exception = $flag;
  }

  //idea taken from PEAR::Getopt
  function readPHPArgv()
  {
    global $argv;
    if(is_array($argv))
      return $argv;

    if(@is_array($_SERVER['argv']))
      return $_SERVER['argv'];

    if(@is_array($GLOBALS['HTTP_SERVER_VARS']['argv']))
      return $GLOBALS['HTTP_SERVER_VARS']['argv'];

    throw new lmbCliException('Could not read cmd args (register_argc_argv=Off?)');
  }

  function getOption($name)
  {
    foreach($this->options as $option)
    {
      if($option->match($name))
        return $option;
    }
  }

  function hasOption($name)
  {
    if($option = $this->getOption($name))
      return true;

    return false;
  }

  function getOptionValue($name, $default = null)
  {
    if($option = $this->getOption($name))
      return $option->getValue();

    return $default;
  }

  function getArgument($index, $default = null)
  {
    return isset($this->arguments[$index]) ? $this->arguments[$index] : $default;
  }

  function addOption($option)
  {
    $this->options[] = $option;
  }

  function getOptions()
  {
    return $this->options;
  }

  function getArguments()
  {
    return $this->arguments;
  }

  protected function _addOptions($args)
  {
    foreach($args as $arg)
    {
      if(is_string($arg))
        $this->options += $this->_objectify($arg);
      elseif(is_object($arg))
        $this->options[] = $arg;
    }
  }

  protected function _parse($argv)
  {
    $this->_reset();

    $postponed_option = null;

    for($i = 0; $i < count($argv); $i++)
    {
      $arg = $argv[$i];

      if($this->_extractLongOption($arg, $name, $value))
      {
        $postponed_option = $this->_addLongOption($name);

        if(isset($value))
          $postponed_option->setValue($value);
        elseif(isset($argv[$i + 1]) && !$this->_isOptionNext($argv,$i))
        {
          $i++;
          if(!$this->_hasOptionsAfter($argv, $i - 1))
            $this->arguments[] = $argv[$i];
          $postponed_option->setValue($argv[$i]);
        }
      }
      elseif($this->_extractShortOption($arg, $name, $value))
      {
        $postponed_option = $this->_addShortOption($name);

        if(isset($value))
          $postponed_option->setValue($value);
        elseif(isset($argv[$i + 1]) && !$this->_isOptionNext($argv,$i))
        {
          $i++;
          if(!$this->_hasOptionsAfter($argv, $i - 1))
            $this->arguments[] = $argv[$i];
          $postponed_option->setValue($argv[$i]);
        }
      }
      else
      {
        $this->arguments[] = $arg;
      }
      unset($postponed_option);
    }
  }

  protected function _extractLongOption($arg, &$option, &$value = null)
  {
    if(!preg_match('~^--([a-zA-Z0-9][-_a-zA-Z0-9]+)(=(.*))?$~', $arg, $m))
      return false;

    $option = $m[1];
    $value = isset($m[3]) ? $m[3] : null;
    return true;
  }

  protected function _extractShortOption($arg, &$option, &$value = null)
  {
    if(!preg_match('~^-([a-zA-Z0-9][^=\s]*)((=|\s+)(.*))?$~', $arg, $m))
      return false;

    $option = $m[1];
    $value = isset($m[4]) ? $m[4] : null;
    return true;
  }

  protected function _maybeArgumentNext($argv, $i)
  {
    return (isset($argv[$i+1]) && strpos($argv[$i+1], '-') === false);
  }

  protected function _isOptionNext($argv, $i)
  {
    if(!isset($argv[$i + 1]))
      return false;
    return '-' === $argv[$i + 1]{0};
  }

  protected function _hasOptionsAfter($argv, $i)
  {
    while($i < count($argv))
    {
      if('-' === $argv[$i]{0})
        return true;
      $i++;
    }
    return false;
  }

  protected function _reset()
  {
    $this->arguments = array();
    foreach($this->options as $option)
      $option->reset();
  }

  protected function _addLongOption($name)
  {
    $option = $this->_approveOption($name);
    return $option;
  }

  protected function _addShortOption($name)
  {
    list($glued, $last) = $this->_getGluedOptions($name);
    foreach($glued as $name)
      $this->_approveOption($name);

    $option = $this->_approveOption($last);
    return $option;
  }

  protected function _approveOption($name)
  {
    $option = new lmbCliOption($name);
    $this->addOption($option);
    return $option;
  }

  protected function _getGluedOptions($glue)
  {
    $glued = array();
    for($j=0;$j<strlen($glue)-1;$j++)
      $glued[] = $glue{$j};

    $last = substr($glue, -1, 1);

    return array($glued, $last);
  }
}


