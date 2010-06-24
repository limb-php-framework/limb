<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
require_once('limb/macro/src/tags/form/lmbMacroFormElementTag.class.php');

/**
 * @tag date3select
 * @forbid_end_tag
 * @req_attributes id
 * @package calendar
 * @version $Id: $
*/
class lmbDate3SelectTag extends lmbMacroFormElementTag
{
  protected $html_tag = 'input';    
  protected $widget_class_name = 'lmbDate3SelectWidget';
  protected $widget_include_file = 'limb/calendar/src/lmbDate3SelectWidget.class.php';  

  function preParse($compiler)
  {
    $this->set('type', 'hidden');
    
    parent :: preParse($compiler);
  }

  protected function _generateAfterClosingTag($code)
  {
    parent :: _generateAfterClosingTag($code);
    $widget = $this->getRuntimeVar();
    $code->writePHP("{$widget}->renderDate3Select();\n");
  }
}
