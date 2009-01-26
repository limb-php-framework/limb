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
 * Macro analog for html <textarea> tag
 * @tag textarea
 * @package macro
 * @version $Id$
 */
class lmbMacroTextAreaTag extends lmbMacroFormElementTag
{
  protected $html_tag = 'textarea';
  protected $widget_class_name = 'lmbMacroTextAreaWidget';
  protected $widget_include_file = 'limb/macro/src/tags/form/lmbMacroTextAreaWidget.class.php';
  
  function preParse($compiler)
  { 
    parent :: preParse($compiler);
  
    // always has closing tag
    $this->has_closing_tag = true;
  }
  
  protected function _generateContent($code)
  {
    $textarea = $this->getRuntimeVar(); 
    $code->writePHP("echo htmlspecialchars({$textarea}->getValue(), ENT_QUOTES);\n");     
  }
}
