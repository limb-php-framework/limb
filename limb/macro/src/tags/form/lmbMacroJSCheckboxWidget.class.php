<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

lmb_require('limb/macro/src/tags/form/lmbMacroCheckableInputWidget.class.php');

/**
 * class lmbMacroJSCheckboxWidget.
 *
 * @package macro
 * @version $Id$
 */
class lmbMacroJSCheckboxWidget extends lmbMacroCheckableInputWidget
{
  protected $skip_render = array('name', 'checked_value');
  
  function renderAttributes()
  {
    parent :: renderAttributes();

    $hidden_id = '_' . $this->getRuntimeId();
    echo " onchange=\"this.form.elements['{$hidden_id}'].value = 1*this.checked\"";
  }

  function renderHidden()
  {
    $hidden_id = '_' . $this->getRuntimeId();
    $hidden_name = $this->getAttribute('name');
    
    if($this->isChecked())
      $value = 1;
    else
      $value = 0;
      
    echo "<input type=\"hidden\" id=\"{$hidden_id}\" name=\"{$hidden_name}\" value=\"{$value}\" />";
  }
}

