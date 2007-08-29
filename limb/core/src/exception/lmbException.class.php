<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * class lmbException.
 *
 * @package core
 * @version $Id: lmbException.class.php 6243 2007-08-29 11:53:10Z pachanga $
 */
class lmbException extends Exception
{
  protected $params = array();

  function __construct($message, $params = array(), $code = 0)
  {
    if(is_array($params) && sizeof($params))
    {
      $this->params = $params;
      $message .= "\n[params: " . var_export($params, true) . "]\n";
    }

    $this->backtrace = debug_backtrace();
    parent :: __construct($message, $code);
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

  function getNiceTraceAsString()
  {
    $html = php_sapi_name() != 'cli';

    $str = '';
    foreach($this->backtrace as $bc)
    {
      if(isset($bc['class']))
      {
        $s = ($html ? "<b>%s</b>" : "%s") . "::";
        $str .= sprintf($s, $bc['class']);
      }

      if (isset($bc['function']))
      {
        $s = ($html ? "<b>%s</b>" : "%s");
        $str .= sprintf($s, $bc['function']);
      }

      $str .= ' (';
      if(isset($bc['args']))
      {
        foreach($bc['args'] as $arg)
        {
          $s = ($html ? "<i>%s</i>, " : "%s, ");
          $type = gettype($arg);

          if($type == "string")
            $str .= sprintf($s, '"' . preg_replace('~^(.{150})(.*)$~', '$1...', $arg) . '"');
          elseif($type == "object")
            $str .= sprintf($s, get_class($arg));
          elseif($type == "array")
          {
            $arr = array();
            foreach($arg as $key => $value)
              $arr[] = "[$key] => $value";

            $str .= sprintf($s, implode(',', $arr));
          }
          elseif($type == "integer" || $type == "float")
            $str .= sprintf($s, $arg);
          else
            $str .= sprintf($s, $type);
        }
        $str = substr($str, 0, -2);
      }

      $str .= ')';
      $str .= ': ';
      $str .= '[ ';
      if (isset($bc['file'])) {
        $dir = substr(dirname($bc['file']), strrpos(dirname($bc['file']), '/') + 1);
        $file = basename($bc['file']);
        if ($html) $str .= "<a href=\"file:/" . $bc['file'] . "\">";
        $str .= $dir . '/' . $file;
        if ($html) $str .= "</a>";
      }
      $str .= isset($bc['line']) ? ', ' . $bc['line'] : '';
      $str .= ' ] ';
      $str .= ($html ? "<br />\n" : "\n");
    }
    return $str;
  }
}

