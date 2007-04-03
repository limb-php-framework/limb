<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbException.class.php 4991 2007-02-08 15:35:35Z pachanga $
 * @package    core
 */

class lmbException extends Exception
{
  protected $params = array();

  function __construct($message, $params = array(), $code = 0)
  {
    if(is_array($params) && sizeof($params))
      $this->params = $params;

    $this->backtrace = debug_backtrace();

    parent :: __construct($message, $code);
  }

  function getParams()
  {
    return $this->params;
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

  function __toString()
  {
    return parent :: __toString() .
           "\n[params: " . var_export($this->params, true) . "]\n";
  }
}
?>