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
 *  This class implements a simple PHP wrapper for the calendar.  It
 *  allows you to easily include all the calendar files and setup the
 *  calendar by instantiating and calling a PHP object.
 * @package calendar
 * @version $Id$
 */
class lmbCalendarWidget extends lmbMacroInputWidget
{
  protected $newline = "\n";
  protected $calendar_lib_path;

  protected $calendar_file;
  protected $calendar_lang_file;
  protected $calendar_setup_file;
  protected $calendar_theme_file;
  protected $calendar_options = array();

  function renderCalendar()
  {
    $this->_initCalendar();
    echo $this->loadFiles();
    echo $this->makeButton(array(),
                           array('src' => $this->getAttribute('src')));
  }

  protected function _initCalendar()
  {
    $theme = 'calendar-win2k-1';
    $calendar_lib_path = '/shared/calendar/js/';
    $lang = $this->getAttribute('lang') ? $this->getAttribute('lang') : 'en';
    $format = $this->getAttribute('format') ? $this->getAttribute('format') : '%Y-%m-%d';
    
    if($this->getBoolAttribute('stripped'))
    {
      $this->calendar_file = 'calendar_stripped.js';
      $this->calendar_setup_file = 'calendar-setup_stripped.js';
    }
    else
    {
      $this->calendar_file = 'calendar.js';
      $this->calendar_setup_file = 'calendar-setup.js';
    }
    
    $this->calendar_lang_file = 'lang/calendar-' . $lang . '.js';
    $this->calendar_theme_file = $theme.'.css';
    $this->calendar_lib_path = preg_replace('/\/+$/', '/', $calendar_lib_path);

    
    $this->setOption('ifFormat', $format);
    $this->setOption('daFormat', $format);
  }

  function setOption($name, $value)
  {
    $this->calendar_options[$name] = $value;
  }

  function loadFiles()
  {
    static $rendered = false;

    $code  = '';

    if(!$rendered)
    {
      $code  = '<link rel="stylesheet" type="text/css" media="all" href="' .
                $this->calendar_lib_path . $this->calendar_theme_file .
                 '" />' . $this->newline;
      $code .=  '<script type="text/javascript" src="' .
                $this->calendar_lib_path . $this->calendar_file .
                '"></script>' . $this->newline;
      $code .= '<script type="text/javascript" src="' .
                $this->calendar_lib_path . $this->calendar_lang_file .
                '"></script>' . $this->newline;
      $code .= '<script type="text/javascript" src="' .
               $this->calendar_lib_path . $this->calendar_setup_file .
               '"></script>';
    }

    $rendered = true;

    return $code;
  }

  function makeButton($cal_options = array(), $field_attributes = array())
  {
    $field_id = $this->getRuntimeId();
    $id = $this->_genId();
    
    if(isset($field_attributes['src']) && $field_attributes['src'])
      $src = $field_attributes['src'];
    else
      $src = $this->calendar_lib_path . 'img.gif';
    
    $out = '<a href="#" id="'. $this->_triggerId($id) . '">' .
        '<img align="middle" border="0" src="' . $src . '" alt="" hspace="3"/></a>';

    $options = array_merge($cal_options,
                           array('inputField' => $field_id,
                                 'button'     => $this->_triggerId($id)));
    return $out . $this->_makeCalendar($options);
  }

  function _makeCalendar($other_options = array())
  {
    $js_options = $this->_makeJsHash(array_merge($this->calendar_options, $other_options));
    $code  = '<script type="text/javascript">Calendar.setup({' .
             $js_options .
             '});</script>';
    return $code;
  }

  function _fieldId($id) { return 'f-calendar-field-' . $id; }
  function _triggerId($id) { return 'f-calendar-trigger-' . $id; }
  function _genId() { static $id = 0; return ++$id; }

  function _makeJsHash($array)
  {
    $jstr = '';
    reset($array);
    while(list($key, $val) = each($array))
    {
      if(is_bool($val))
        $val = $val ? 'true' : 'false';
      else if(!is_numeric($val))
        $val = '"'.$val.'"';
      if($jstr) $jstr .= ',';
      $jstr .= '"' . $key . '":' . $val;
    }
    return $jstr;
  }

  function _makeHtmlAttr($array)
  {
    $attrstr = '';
    reset($array);
    while(list($key, $val) = each($array))
    {
      $attrstr .= $key . '="' . $val . '" ';
    }
    return $attrstr;
  }
}


