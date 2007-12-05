<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

lmb_require('limb/macro/src/tags/form/lmbMacroFormFieldWidget.class.php');

//--------------------------------------------------------------------------------
/**
 * Represents an HTML select tag where only a single option can
 * be selected at runtile
 * @package macro
 * @version $Id$
 */
class lmbMacroSingleSelectWidget extends lmbMacroFormFieldWidget
{
  protected $options = array();

  protected $default_selection = null;
  
  protected $skip_render = array('value', 'options', 'value_field');

  function setOptions($options)
  {
    $this->options = $options;
  }
  
  function getOptions()
  {
    return $this->options;
  }

  function addToOptions($key, $value = null)
  {
    if(is_null($value))
      $this->options[] = $key;
    else
      $this->options[$key] = $value;
  }

  function prependToOptions($key, $value = null)
  {
    if(is_null($value))
      array_unshift($this->options, $key);
    else
      $this->options = array($key => $value) + $this->options;
  }

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
  
  /**
  * Renders the contents of the the select tag, option tags being built by
  * the option handler. Called from with a compiled template render function.
  */
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
  
  protected function _renderOption($key, $option, $selected)
  {
    echo '<option value="';
    echo htmlspecialchars($key, ENT_QUOTES);
    echo '"';
    if ($selected) {
        echo " selected=\"true\"";
    }
    echo '>';
    if (empty($option)) {
        echo htmlspecialchars($key, ENT_QUOTES);
    } else {
        echo htmlspecialchars($option, ENT_QUOTES);
    }
    echo '</option>';     
  }
}


