<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
require_once('limb/calendar/src/lmbCalendarWidget.class.php');
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
  protected $widget_class_name = 'lmbMacroInputWidget';
  protected $widget_include_file = 'limb/macro/src/tags/form/lmbMacroInputWidget.class.php';  
  
  function preParse($compiler)
  {
    $this->set('type', 'text');
    
    parent :: preParse($compiler);
  }

  protected function _generateAfterClosingTag($code)
  {
    parent :: _generateAfterClosingTag($code);

    if(!$lang = $this->get('lang'))
      $lang = 'en';

    if(!$this->has('stripped'))
      $stripped = true;
    else
      $stripped = $this->getBool('stripped');

    $widget = new lmbCalendarWidget($lang, $stripped);

    if($format = $this->get('format'))
    {
      $widget->setOption('ifFormat', $format);
      $widget->setOption('daFormat', $format);
    }
    else
    {
      $widget->setOption('ifFormat', '%Y-%m-%d');
      $widget->setOption('daFormat', '%Y-%m-%d');
    }

    $code->writeHTML($widget->loadFiles() .
                     $widget->makeButton($this->get('id'), 
                                         array(),
                                         array('src' => $this->get('src'))));
  }

}


