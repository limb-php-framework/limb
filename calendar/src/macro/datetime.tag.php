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
 * @tag datetime
 * @forbid_end_tag
 * @package calendar
 * @version $Id$
 */
class lmbMacroDatetimeTag extends lmbMacroFormElementTag
{
  protected $html_tag = 'input';    
  protected $widget_class_name = 'lmbCalendarWidget';
  protected $widget_include_file = 'limb/calendar/src/lmbCalendarWidget.class.php';  
  
  function preParse($compiler)
  {
    $this->set('type', 'text');
    
    parent :: preParse($compiler);
  }

  protected function _generateAfterClosingTag($code)
  {
    parent :: _generateAfterClosingTag($code);

    $widget = $this->getRuntimeVar();
    $code->writePHP("{$widget}->renderCalendar();\n");
  }
}
