<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

lmb_require('limb/macro/src/tags/form/lmbMacroFormElementTag.class.php');

/**
 * Macro analog for html <input> tag
 * @tag input
 * @forbid_end_tag  
 * @package macro
 * @version $Id$
 */
class lmbMacroInputTag extends lmbMacroFormElementTag
{
  protected $html_tag = 'input';
  protected $widget_include_file = 'limb/macro/src/tags/form/lmbMacroInputWidget.class.php';
  
  function preParse($compiler)
  {
    $type = strtolower($this->get('type'));
    switch ($type)
    {
      case 'text': 
      case 'hidden':
      case 'image':
      case 'button':
        $this->widget_class_name = 'lmbMacroInputWidget';
        break;
      case 'checkbox':
      case 'radio':
        $this->widget_include_file = 'limb/macro/src/tags/form/lmbMacroCheckableInputWidget.class.php';
        $this->widget_class_name = 'lmbMacroCheckableInputWidget';
        break;
      case 'password':
      case 'submit':
      case 'reset':
      case 'file':
        $this->widget_class_name = 'lmbMacroFormElementWidget';
        break;
      default:
        $this->raise('Unrecognized type attribute for input tag');
    }
  }
}

