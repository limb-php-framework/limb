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
class lmbMacroSingleSelectWidget extends lmbMacroSelectWidget
{
  protected $default_selection = null;
  
  function addToDefaultSelection($selection)
  {
    $this->default_selection = $selection;
  }
  
  function getValue()
  {
    $value = parent :: getValue();
    
    if(is_null($value))
      $value = $this->default_selection;

    if(is_scalar($value))
      return $value;
    
    if(!$value_field = $this->getAttribute('value_field'))
      $value_field = 'id';

    if((is_array($value) || ($value instanceof ArrayAccess)) && isset($value[$value_field]))
      return $value[$value_field];
  }
  
  function renderOptions()
  {
    $value = $this->getValue();

    foreach($this->options as $key => $option)
    {
      //special case, since in PHP "0 == 'bar'"
      $selected = ((string)$key) == $value;
      $this->_renderOption($key, $option, $selected);
    }
  }
}


