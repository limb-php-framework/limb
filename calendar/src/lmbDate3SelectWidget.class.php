<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

lmb_require('limb/macro/src/tags/form/lmbMacroInputWidget.class.php');

/**
 * This class allows you to enter the date by three select fields.
 * @package calendar
 * @version $Id$
 */
class lmbDate3SelectWidget extends lmbMacroInputWidget
{
  protected $calendar_file = 'calendar.js';
  protected $newline = "\n";

  function renderDate3Select()
  {
    $this->_initDate3Select();
    echo $this->loadFiles();
    echo $this->makeFields();
  }

  protected function _initDate3Select()
  {
    $this->calendar_lib_path = $this->getAttribute('calendar_lib_path') ? $this->getAttribute('calendar_lib_path') : '/shared/calendar/js/';
    $lang = $this->getAttribute('lang') ? $this->getAttribute('lang') : 'en';
    $this->show_default = $this->getBoolAttribute('show_default');
    $this->dw_lang_file = 'lang/calendar-' . $lang . '.js';

    $this->dw_year_class = ('' != $this->getAttribute('year_class')) ? $this->getAttribute('year_class') : $this->getAttribute('class');
    $this->dw_month_class = ('' != $this->getAttribute('month_class')) ? $this->getAttribute('month_class') : $this->getAttribute('class');
    $this->dw_day_class = ('' != $this->getAttribute('day_class')) ? $this->getAttribute('day_class') : $this->getAttribute('class');

    $this->dw_lib_path = preg_replace('/\/+$/', '/', $this->calendar_lib_path).'datewidget.js';
    $min_year = intval($this->getAttribute('min_year'));
    $this->min_year = $min_year > 0 ? $min_year : date('Y', 0);
    $max_year = intval($this->getAttribute('max_year'));
    $this->max_year = $max_year > 0 ? $max_year : date('Y');
    $this->format = ('' != $this->getAttribute('format')) ? $this->getAttribute('format') : '%Y-%m-%d';
  }

  function loadFiles()
  {
    static $rendered = false;

    $code  = '';

    if(!$rendered)
    {
      $code = '<script type="text/javascript" src="' .
                $this->calendar_lib_path . $this->calendar_file .
                '"></script>' . $this->newline;
      $code .= '<script type="text/javascript" src="' .
                $this->calendar_lib_path . $this->dw_lang_file .
                '"></script>' . $this->newline;
      $code .= '<script type="text/javascript" src="' .
                $this->dw_lib_path . '"></script>' . $this->newline;
    }

    $rendered = true;

    return $code;
  }

  function makeFields()
  {
    $field_name = $this->getRuntimeId();
    $out = '<select name="' . $field_name . '_day" id="' . $field_name . '_day" class="' . $this->dw_day_class . '" onchange="DateWidget_Action(\'' . $field_name . '\', \'handle_change\', \'day\');"></select>' . $this->newline;
    $out .= '<select name="' . $field_name . '_month" id="' . $field_name . '_month" class="' . $this->dw_month_class . '" onchange="DateWidget_Action(\'' . $field_name . '\', \'handle_change\', \'month\');"></select>' . $this->newline;
    $out .= '<select name="' . $field_name . '_year" id="' . $field_name . '_year" class="' . $this->dw_year_class . '" onchange="DateWidget_Action(\'' . $field_name . '\', \'handle_change\', \'year\');"></select>' . $this->newline;
    $out .= '<script type="text/javascript">DateWidget_Init("' . $field_name . '", ' . ($this->show_default ? 'true' : 'false') . ', ' . $this->min_year . ', ' . $this->max_year . ', \'' . $this->format . '\');</script>' . $this->newline;
    return $out;
  }
}
