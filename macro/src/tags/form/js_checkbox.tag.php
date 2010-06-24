<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

lmb_require('limb/macro/src/tags/form/lmbMacroFormElementTag.class.php');

/**
 * Checkbox that always sends something as a value. 
 * Actually generates hidden input as well as checkbox. The checkbox used only to change hidden input value 
 * @tag js_checkbox
 * @forbid_end_tag
 * @package macro
 * @version $Id$
 */
class lmbMacroJSCheckboxTag extends lmbMacroFormElementTag
{
  protected $html_tag = 'input'; 
  protected $widget_class_name = 'lmbMacroJSCheckboxWidget';
  protected $widget_include_file = 'limb/macro/src/tags/form/lmbMacroJSCheckboxWidget.class.php'; 
  
  function preParse($compiler)
  {
    parent :: preParse($compiler);

    $this->set('type', 'checkbox');
  }

  function _generateContent($code_writer)
  {   
    $code_writer->writePHP("{$this->getRuntimeVar()}->renderHidden();\n");
  }
}


