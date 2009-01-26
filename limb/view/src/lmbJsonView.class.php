<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/view/src/lmbView.class.php');
/**
 * class lmbJsonView.
 *
 * @package view
 * @version $Id$
 */
class lmbJsonView extends lmbView 
{  
  protected $use_emulation = false;
  
  function __construct() {}
  
  function useEmulation($value)
  {
    $this->use_emulation = $value;
  }
  
  protected function _encodeEmulation($values)
  {    
    if (is_null($values)) return '[]';
    if ($values === false) return 'false';
    if ($values === true) return 'true';
    if (is_scalar($values))
    {
      if (is_float($values))
      {
        // Always use "." for floats.
        return floatval(str_replace(",", ".", strval($values)));
      }

      if (is_string($values))
      {
        static $jsonReplaces = array(array("\\", "/", "\n", "\t", "\r", "\b", "\f", '"'), array('\\\\', '\\/', '\\n', '\\t', '\\r', '\\b', '\\f', '\"'));
        return '"' . str_replace($jsonReplaces[0], $jsonReplaces[1], $values) . '"';
      }
      else
        return $values;
    }
    $isList = true;
    for ($i = 0, reset($values); $i < count($values); $i++, next($values))
    {
      if (key($values) !== $i)
      {
        $isList = false;
        break;
      }
    }
    $result = array();
    if ($isList)
    {
      foreach ($values as $v) $result[] = json_encode($v);
      return '[' . join(',', $result) . ']';
    }
    else
    {
      foreach ($values as $k => $v) $result[] = json_encode($k).':'.json_encode($v);
      return '{' . join(',', $result) . '}';
    }
  }

  function render()
  {
    if(!function_exists('json_encode') || $this->use_emulation)
      return $this->_encodeEmulation($this->getVariables());
    else
      return json_encode($this->getVariables());     
  }
}

