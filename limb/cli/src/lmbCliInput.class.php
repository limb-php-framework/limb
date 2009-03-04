<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

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
    $args = func_get_args();
    $this->_addOptions($args);
  }

  function strictMode($flag = true)
  {
    $this->strict_mode = $flag;
  }

  function setMinimumArguments($minimum_args)
  {
    $this->minimum_args = $minimum_args;
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
      $this->_validate();
    }
    catch(lmbCliException $e)
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
      return $option->isPresent();

    return false;
  }

  //@obsolete
  function isOptionPresent($name)
  {
    return $this->hasOption($name);
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

  protected function _validate()
  {
    if(!is_null($this->minimum_args) && $this->minimum_args > sizeof($this->arguments))
      throw new lmbCliException("Minimum {$this->minimum_args} required");

    foreach($this->options as $option)
      $option->validate();
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

  protected function _objectify($str)
  {
    $opts = array();
    foreach(explode(';', $str) as $item)
    {
      if(!$item)
        continue;

      if(preg_match('~^(?:((\w)\|([a-zA-Z0-9-_]+))|(\w\b)|([a-zA-Z0-9-_]+)?)(=)?~', $item, $m))
      {
        $req = isset($m[6]) ? lmbCliOption :: VALUE_REQ : lmbCliOption :: VALUE_NO;

        if($m[1])
          $opt = new lmbCliOption($m[2], $m[3], $req);
        elseif($m[4])
          $opt = new lmbCliOption($m[4], $req);
        elseif($m[5])
          $opt = new lmbCliOption($m[5], $req);
        else
          throw new lmbCliException("Invalid option descriptor '$item'");

        $opts[] = $opt;
      }
      else
        throw new lmbCliException("Invalid option descriptor '$item'");
    }
    return $opts;
  }

  protected function _parse($argv)
  {
    $this->_reset();

    $postponed_option = null;

    for($i=0;$i<sizeof($argv);$i++)
    {
      $arg = $argv[$i];

      if($this->_extractLongOption($arg, $name, $value))
      {
        $postponed_option = $this->_addLongOption($name);

        if(isset($value))
        {
          $postponed_option->setValue($value);
          unset($postponed_option);
        }
      }
      elseif($this->_extractShortOption($arg, $name, $value))
      {
        $postponed_option = $this->_addShortOption($name, $value);

        if(isset($value))
        {
          if(!$postponed_option->isValueForbidden())
            $postponed_option->setValue($value);
          elseif($this->_maybeArgumentNext($argv, $i))
          {
            $this->arguments[] = $value;
            $i++;
          }

          unset($postponed_option);
        }
        elseif($postponed_option->isValueForbidden())
          unset($postponed_option);
      }
      elseif(isset($postponed_option) && $this->strict_mode)
      {
        $postponed_option->setValue($arg);
        unset($postponed_option);
      }
      else
      {
        $this->arguments[] = $arg;
      }
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
    return (isset($argv[$i+1]) &&
            strpos($argv[$i+1], '-') === false);
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
    if(!$option = $this->getOption($name))
    {
      if(!$this->strict_mode)
      {
        $option = new lmbCliOption($name);
        $this->addOption($option);
      }
      else
        throw new lmbCliException("Option '{$name}' is illegal");
    }

    $option->touch();
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


