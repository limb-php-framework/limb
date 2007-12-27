<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

lmb_require('limb/macro/src/tags/form/lmbMacroFormElementWidget.class.php');

/**
 * @package macro
 * @version $Id$
 */
abstract class lmbMacroSelectWidget extends lmbMacroFormElementWidget
{
  protected $options = array();

  protected $skip_render = array('value', 'options', 'value_field');

  function getName()
  {
    $name = parent :: getName();
    return str_replace('[]', '', $name) ;
  } 
  
  function setOptions($options)
  {
    if(!is_array($options))
      $options = array();
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

  abstract function addToDefaultSelection($selection);
  
  abstract function renderOptions();
  
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


