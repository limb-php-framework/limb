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
    if(is_array($limit_or_backtrace))
    {
      $this->backtrace = $limit_or_backtrace;
      $limit = $limit_or_offset;
    }
    else
    {
      $this->backtrace = debug_backtrace();
      $limit = $limit_or_backtrace;
      $offset = (int)$limit_or_offset;
    }

    //we skip this function call also
    for($i=0; $i<($offset+1); $i++)
      array_shift($this->backtrace);

    if(!is_null($limit))
      $this->backtrace = array_splice($this->backtrace, 0, $limit);
  }

  function get()
  {
    return $this->backtrace;
  }

  function getContext()
  {
    reurn (sizeof($this->backtrace)) ? $this->backtrace[0] : '';
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
        elseif(is_bool($arg))
          $trace_string .= $arg ? 'TRUE' : 'FALSE';
        else
        {
          $trace_string .= '"';
          $trace_string .= htmlspecialchars(substr((string) @$arg, 0, 100));

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
}


