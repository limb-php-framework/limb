<?php
/**
 *
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

class lmbBaseMacroTagTest extends lmbBaseMacroTest
{
  protected function _getRenderedWidgetAttributeValue($widget, $attribute_name)
  {
    ob_start();
    $widget->renderAttributes();
    $result = ob_get_clean();
    $matches = array();
    preg_match('/'.$attribute_name.'=\"([^"]*)\"/i', $result, $matches);
    return isset($matches[1]) ? $matches[1] : null;
  }
}