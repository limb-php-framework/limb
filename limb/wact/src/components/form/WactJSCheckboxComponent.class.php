<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactJSCheckboxComponent.class.php 5688 2007-04-19 11:15:15Z serega $
 * @package    wact
 */

class WactJSCheckboxComponent extends WactCheckableInputTagComponent
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

    // title has no meaning in <input type="hidden"/> field, so it should be copied into checkbox
    $title = $this->getAttribute('title');
    $title = $title ? 'title=\''.$title.'\'' : '';

    echo "<input type='checkbox' id='{$box_id}' {$checked} {$js} {$title}>";
  }
}
?>