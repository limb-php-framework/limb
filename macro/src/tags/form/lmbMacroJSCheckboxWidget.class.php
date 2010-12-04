<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
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
  protected $hidden_id;

  function renderAttributes()
  {
    parent :: renderAttributes();
    
    if (!$this->hasAttribute('value'))
      echo ' value="1"';
    
    $this->hidden_id = uniqid('js_checkbox_');
    echo " onchange=\"this.form.elements['{$this->hidden_id}'].value=this.checked?this.value:0;\"";
  }

  function renderHidden()
  {
    $hidden_name = $this->getAttribute('name');
    
    $value = 0;
    if($this->isChecked())
    {
      $value = $this->getAttribute('value');
      $value = empty($value) ? 1 : $value;
    }

    echo "<input type=\"hidden\" id=\"{$this->hidden_id}\" name=\"{$hidden_name}\" value=\"{$value}\" />";
  }
}

