<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * class WactJSCheckboxComponent.
 *
 * @package wact
 * @version $Id: WactJSCheckboxComponent.class.php 7686 2009-03-04 19:57:12Z korchasa $
 */
class WactJSCheckboxComponent extends WactCheckableInputComponent
{
  function renderAttributes()
  {
    $this->setAttribute('id', '_' . $this->getId());

    if($this->isChecked())
      $this->setAttribute('value', "1");

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

    echo "<input type='checkbox' id='{$box_id}' {$checked} {$js} {$title} />";
  }
}

