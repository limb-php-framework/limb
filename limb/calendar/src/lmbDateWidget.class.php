<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * This class allows you to enter the date by three select fields.
 * @package calendar
 * @version $Id$
 */
class lmbDateWidget {

  protected $newline = "\n";

  protected $dw_lang_file;
  protected $dw_year_class;
  protected $dw_month_class;
  protected $dw_day_class;
  protected $dw_lib_path;
  protected $calendar_lib_path = '/shared/calendar/js/';
  protected $calendar_file = 'calendar.js';
  protected $show_default = false;
  protected $min_year;
  protected $max_year;

  function __construct($lang = 'en',
                       $year_class = "",
                       $month_class = "",
                       $day_class = "",
                       $show_default = false,
                       $lib_path = '/shared/calendar/js/')
  {
    $this->show_default = $show_default;
    $this->calendar_lib_path = $lib_path;
    $this->dw_lang_file = 'lang/calendar-' . $lang . '.js';
    $this->dw_year_class = $year_class;
    $this->dw_month_class = $month_class;
    $this->dw_day_class = $day_class;
    $this->dw_lib_path = preg_replace('/\/+$/', '/', $this->calendar_lib_path).'datewidget.js';
    $this->min_year = 1950;
    $this->max_year = 2000;
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

  function makeFields($field_name)
  {
    $out = '<select name="' . $field_name . '_day" id="' . $field_name . '_day" class="' . $this->dw_day_class . '" onchange="DateWidget_Action(\'' . $field_name . '\', \'handle_change\', \'day\');"></select>' . $this->newline;
    $out .= '<select name="' . $field_name . '_month" id="' . $field_name . '_month" class="' . $this->dw_month_class . '" onchange="DateWidget_Action(\'' . $field_name . '\', \'handle_change\', \'month\');"></select>' . $this->newline;
    $out .= '<select name="' . $field_name . '_year" id="' . $field_name . '_year" class="' . $this->dw_year_class . '" onchange="DateWidget_Action(\'' . $field_name . '\', \'handle_change\', \'year\');"></select>' . $this->newline;
    $out .= '<script type="text/javascript">DateWidget_Init("' . $field_name . '", ' . ($this->show_default ? 'true' : 'false') . ', ' . $this->min_year . ', ' . $this->max_year . ');</script>' . $this->newline;
    return $out;
  }


  function setMinYear($year)
  {
    if ($year > 0)
    {
      $this->min_year = $year;
    }
  }

  function setMaxYear($year)
  {
    if ($year > 0)
    {
      $this->max_year = $year;
    }
  }

}