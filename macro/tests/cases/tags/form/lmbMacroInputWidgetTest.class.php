<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

class lmbMacroInputWidgetTest extends lmbBaseMacroTagTest
{
  function testRenderAttributes_ValueAttribute()
  {
    $widget = new lmbMacroInputWidget('my_id');
    $this->assertEqual('', $this->_getRenderedWidgetAttributeValue($widget, 'value'));

    $widget->setAttribute('value', $value = 'my_value');
    $this->assertEqual('my_value', $this->_getRenderedWidgetAttributeValue($widget, 'value'));
  }
}