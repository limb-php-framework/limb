<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
require_once('limb/wact/src/tags/form/input.tag.php');
require_once('limb/calendar/src/lmbDateWidget.class.php');

/**
 * @tag date_select
 * @forbid_end_tag
 * @package calendar
 * @req_attributes id
 */
class DateSelectTag extends WactInputTag
{
  function getRenderedTag()
  {
    return 'input';
  }

  function prepare()
  {
    $this->setAttribute('type', 'hidden');
    parent :: prepare();
  }

  function generateAfterCloseTag($code)
  {
    parent :: generateAfterCloseTag($code);

    if(!$lang = $this->getAttribute('lang'))
      $lang = 'en';

    $year_class = $this->getAttribute('year_class');
    $month_class = $this->getAttribute('year_class');
    $day_class = $this->getAttribute('year_class');
    $show_default = $this->getBoolAttribute('show_default');
    $min_year = $this->getAttribute('min_year');
    $max_year = $this->getAttribute('max_year');

    $widget = new lmbDateWidget($lang, $year_class, $month_class, $day_class, $show_default);
	if ($min_year)
	{
	  $widget -> setMinYear(intval($min_year));
	}
	if ($max_year)
	{
	  $widget -> setMaxYear(intval($max_year));
	}
    

    $code->writeHTML($widget->loadFiles() .
                     $widget->makeFields($this->getAttribute('id') ));

  }

}


