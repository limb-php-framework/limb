<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

lmb_require('limb/macro/src/tags/form/lmbMacroSelectWidget.class.php');

/**
 * Represents an HTML select tag where only a single option can
 * be selected at runtile
 * @package macro
 * @version $Id$
 */
class lmbMacroMultipleSelectWidget extends lmbMacroSelectWidget
{
  protected $default_selection = array();
  
  function addToDefaultSelection($selection)
  {
    $this->default_selection[] = $selection;
  }
  
  function getValue()
  {
    $values = parent :: getValue();
    
    if(!is_object($values) && !is_array($values))
      $values = $this->default_selection;

    if(is_object($values) && !($values instanceof Iterator))
      $values = $this->default_selection;
    
    if(!$value_field = $this->getAttribute('value_field'))
      $value_field = 'id';
    
    $result = array();
    foreach($values as $value)
    {
      if(is_scalar($value))
        $result[] = $value;        
      elseif((is_array($value) || $value instanceof ArrayAccess)  && isset($value[$value_field]))
        $result[] = $value[$value_field];        
    }
    return $result;
  }
  
  function renderOptions()
  {
    $values = $this->getValue();

    foreach($this->options as $key => $option)
    {
      //special case, since in PHP "0 == 'bar'"
      $selected = in_array((string)$key, $values);
      $this->_renderOption($key, $option, $selected);
    }
  }
}


