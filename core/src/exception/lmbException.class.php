<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * class lmbException.
 *
 * @package core
 * @version $Id: lmbException.class.php 8022 2010-01-16 01:04:55Z korchasa $
 */
class lmbException extends Exception
{
  protected $original_message;
  protected $params = array();
  protected $file;
  protected $line;
  protected $backtrace;

  function __construct($message, $params = array(), $code = 0, $hide_calls_count = 0)
  {
    $this->original_message = $message;
    $this->params = $params;

    $this->backtrace = array_slice(debug_backtrace(), $hide_calls_count);

    foreach($this->backtrace as $item)
    {
      if (isset($item['file']))
      {
        $this->file = $item['file'];
        $this->line = $item['line'];
        break;
      }
    }

    $message = $this->toNiceString();

    parent :: __construct($message, $code);
  }

  function getOriginalMessage()
  {
    return $this->original_message;
  }

  function getRealFile()
  {
    return $this->file;
  }

  function getRealLine()
  {
    return $this->line;
  }

  function getParams()
  {
    return $this->params;
  }

  function getParam($name)
  {
    if(isset($this->params[$name]))
      return $this->params[$name];
  }

  function getBacktrace()
  {
    return $this->backtrace;
  }

  function getNiceTraceAsString()
  {
    return $this->getBacktraceObject()->toString();
  }

  /**
   * @return lmbBacktrace
   */
  function getBacktraceObject()
  {
    return new lmbBacktrace($this->backtrace);
  }

  function toNiceString($without_backtrace = false)
  {
    $string = get_class($this).': '.$this->getOriginalMessage().PHP_EOL;
    if($this->params)
      $string .= 'Additional params: '.PHP_EOL.lmb_var_export($this->params).PHP_EOL;
    if(!$without_backtrace)
      $string .= 'Backtrace: '.PHP_EOL.$this->getBacktraceObject()->toString();
    return $string;
  }

  function __toString()
  {
    return $this->toNiceString();
  }
}

