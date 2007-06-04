<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactJSCheckboxComponent.class.php 5933 2007-06-04 13:06:23Z pachanga $
 * @package    $package$
 */

class WactJSCheckboxComponent extends WactCheckableInputComponent
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

    if($this->isChecked())
      $checked = 'checked=\'on\'';
    else
      $checked = '';

    $js = "onchange=\"this.form.elements['{$id}'].value = 1*this.checked\"";

    // title has no meaning in <input type="hidden"/> field, so it should be copied into checkbox
    $title = $this->getAttribute('title');
    $title = $title ? 'title=\''.$title.'\'' : '';

    echo "<input type='checkbox' id='{$box_id}' {$checked} {$js} {$title}>";
  }
}
?>