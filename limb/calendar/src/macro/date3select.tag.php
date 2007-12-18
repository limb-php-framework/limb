<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
require_once('limb/calendar/src/lmbDate3SelectWidget.class.php');

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
  protected $widget_class_name = 'lmbMacroInputWidget';
  protected $widget_include_file = 'limb/macro/src/tags/form/lmbMacroInputWidget.class.php';  

  function preParse($compiler)
  {
    $this->set('type', 'hidden');
    
    parent :: preParse($compiler);
  }

  protected function _generateAfterClosingTag($code)
  {
    parent :: _generateAfterClosingTag($code);

    if(!$lang = $this->get('lang'))
      $lang = 'en';

    $year_class = $this->get('year_class');
    $month_class = $this->get('year_class');
    $day_class = $this->get('year_class');
    $show_default = $this->getBool('show_default');
    $min_year = $this->get('min_year');
    $max_year = $this->get('max_year');

    $widget = new lmbDate3SelectWidget($lang, $year_class, $month_class, $day_class, $show_default);
    
    if ($min_year)
      $widget->setMinYear(intval($min_year));
    
    if ($max_year)
      $widget->setMaxYear(intval($max_year));
    

    $code->writeHTML($widget->loadFiles() . $widget->makeFields($this->get('id')));
  }

}


