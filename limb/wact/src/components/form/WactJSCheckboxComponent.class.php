<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactJSCheckboxComponent.class.php 5453 2007-03-30 14:08:59Z serega $
 * @package    wact
 */

class WactJSCheckboxComponent extends WactCheckableFormElement
{
  function renderAttributes()
  {
    unset($this->attributes['value']);
    $this->setAttribute('id', '_' . $this->getId());
    parent :: renderAttributes();
  }

  function renderJsCheckbox()
  {
    $id = $this->getAttribute('id');
    //box_id can be used with <label> tag
    $box_id = $this->getId();

    if($this->_isChecked())
      $checked = 'checked=\'on\'';
    else
      $checked = '';

    $js = "onchange=\"this.form.elements['{$id}'].value = 1*this.checked\"";

    echo "<input type='checkbox' id='{$box_id}' {$checked} {$js}>";

  }

}
?>