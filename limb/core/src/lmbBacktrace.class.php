<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * class lmbBacktrace.
 *
 * @package log
 * @version $Id$
 */
class lmbBacktrace
{
  protected $backtrace = array();

  function __construct($limit_or_backtrace = null, $limit_or_offset = null, $offset = 0)
  {
    lmb_assert_true(!is_object($limit_or_backtrace), "Backtrace can't be a object");

    if(is_array($limit_or_backtrace))
    {
      $this->backtrace = $limit_or_backtrace;
      $limit = $limit_or_offset;
    }
    else
    {
      $this->backtrace = debug_backtrace();
      $limit = $limit_or_backtrace;
      $offset = (int) $limit_or_offset + 1;
    }

    if(is_null($limit))
      $limit = count($this->backtrace) - $offset;

    $this->backtrace = array_splice($this->backtrace, $offset, $limit);
  }

  function get()
  {
    return $this->backtrace;
  }

  function getContext()
  {
    return (sizeof($this->backtrace)) ? $this->backtrace[0] : '';
  }

  function toString()
  {
    $trace_string = '';

    foreach($this->backtrace as $item)
    {
      $trace_string .= '* ';
      $trace_string .= $this->_formatBacktraceItem($item) . "\n";
    }
    return $trace_string;
  }

  function _formatBacktraceItem($item)
  {
    $trace_string = '';

    if(isset($item['class']))
    {
      $trace_string .= $item['class'];
      $trace_string .= "::";
    }

    if(isset($item['function']))
    {
      $trace_string .= $item['function'];
      $trace_string .= "(";
    }

    if(isset($item['args']))
    {
      $sep = '';
      foreach($item['args'] as $arg)
      {
        $trace_string .= $sep;
        $sep = ', ';

        if(is_null($arg))
          $trace_string .= 'NULL';
        elseif(is_array($arg))
          $trace_string .= 'ARRAY[' . sizeof($arg) . ']';
        elseif(is_object($arg))
          $trace_string .= 'OBJECT:' . get_class($arg);
        elseif(is_resource($arg))
        {
          $resource_id = strstr((string) $arg, '#');
          $trace_string .= 'RESOURCE[' . get_resource_type($arg) . "]: $resource_id";
        }
        elseif(is_bool($arg))
          $trace_string .= $arg ? 'TRUE' : 'FALSE';
        else
        {
          $arg = (string) $arg;
          $trace_string .= '"';
          $trace_string .= htmlspecialchars(substr($arg, 0, 100));

          if(strlen($arg) > 100)
            $trace_string .= '...';

          $trace_string .= '"';
        }
      }
    }

    if(isset($item['function']))
    {
      $trace_string .= ")";
    }

    if(isset($item['file']))
    {
      $trace_string .= ' in "' . $item['file'] . '"';
      $trace_string .= " line ";
      $trace_string .= $item['line'];
    }

    return $trace_string;
  }

  static function create($limit = null, $offset = null, $backtrace = null)
  {
    return new self($backtrace, $limit, $offset);
  }
}


