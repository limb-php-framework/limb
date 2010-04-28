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

    $this->hidden_id = uniqid('js_checkbox_');
    echo " onchange=\"this.form.elements['{$this->hidden_id}'].value = 1*this.checked\"";
  }

  function renderHidden()
  {
    $hidden_name = $this->getAttribute('name');

    if($this->isChecked())
      $value = 1;
    else
      $value = 0;

    echo "<input type=\"hidden\" id=\"{$this->hidden_id}\" name=\"{$hidden_name}\" value=\"{$value}\" />";
  }
}

