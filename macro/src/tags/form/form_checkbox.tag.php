<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2012 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

lmb_require('limb/macro/src/tags/form/lmbMacroFormElementTag.class.php');

/**
 * Macro analog for html <input type="checkbox"> tag
 *
 * Checkbox that always sends something as a value.
 * Actually generates hidden input as well as checkbox. The checkbox used only to change hidden input value.
 *
 * @tag form:checkbox
 * @package macro
 */
class lmbMacroFormCheckboxTag extends lmbMacroFormElementTag
{
  protected $html_tag = 'input';

  function preParse($compiler)
  {
    if ($this->_isStandartMode())
    {
      $this->widget_include_file = 'limb/macro/src/tags/form/lmbMacroCheckableInputWidget.class.php';
      $this->widget_class_name = 'lmbMacroCheckableInputWidget';
    }
    else
    {
      $this->widget_include_file = 'limb/macro/src/tags/form/lmbMacroJSCheckboxWidget.class.php';
      $this->widget_class_name = 'lmbMacroJSCheckboxWidget';
    }

    parent :: preParse($compiler);

    $this->set('type', 'checkbox');
  }

  function _generateContent($code_writer)
  {
    if(!$this->_isStandartMode())
      $code_writer->writePHP("{$this->getRuntimeVar()}->renderHidden();\n");
  }

  protected function _isStandartMode()
  {
    return $this->getBool('standart_mode', false);
  }
}
