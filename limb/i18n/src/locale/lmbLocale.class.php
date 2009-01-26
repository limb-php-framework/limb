<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/config/src/lmbIni.class.php');
lmb_require('limb/i18n/src/locale/lmbLocaleSpec.class.php');

/**
 * Handles locale information and can format time, date, numbers and currency
 * for correct display for a given locale. The locale conversion uses plain numerical values for
 * dates, times, numbers and currency, if you want more elaborate conversions consider using the
 * date, time, date_time and currency classes.
 *
 * Countries are specified by the ISO 3166 country Code
 * http://www.iso.ch/iso/en/prods-services/iso3166ma/index.html
 * User-assigned code elements
 * http://www.iso.ch/iso/en/prods-services/iso3166ma/04background-on-iso-3166/reserved-and-user-assigned-codes.html#userassigned
 *
 * language is specified by the ISO 639 language Code
 * http://www.w3.org/WAI/ER/IG/ert/iso639.htm
 *
 * currency/funds are specified by the ISO 4217
 * http://www.bsi-global.com/Technical+Information/Publications/_Publications/tig90.xalter
 * @package i18n
 * @version $Id: lmbLocale.class.php 7486 2009-01-26 19:13:20Z pachanga $
 */
class lmbLocale
{
  protected $is_valid = false;

  public $date_format = ''; // format of dates
  public $short_date_format = ''; // format of short dates
  public $time_format = ''; // format of times
  public $date_time_format = '';
  public $short_date_time_format = '';
  public $short_date_short_time_format = '';
  public $short_time_format = ''; // format of short times
  public $is_monday_first = false; // true if monday is the first day of the week
  public $am_name = 'am';
  public $pm_name = 'pm';
  public $charset = '';
  public $LC_ALL = '';
  // numbers
  public $decimal_symbol = '';
  public $thousand_separator = '';
  public $fract_digits = '';
  public $negative_symbol = '';
  public $positive_symbol = '';
  // currency
  public $currency_name = '';
  public $currency_short_name = '';
  public $currency_decimal_symbol = '';
  public $currency_thousand_separator = '';
  public $currency_fract_digits = '';
  public $currency_negative_symbol = '';
  public $currency_positive_symbol = '';
  public $currency_symbol = '';
  public $currency_positive_format = '';
  public $currency_negative_format = '';
  // help arrays
  public $short_month_names = array();
  public $long_month_names = array();
  public $short_day_names = array();
  public $long_day_names = array();
  public $week_days = array(0, 1, 2, 3, 4, 5, 6);
  public $months = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11);

  public $country = '';
  public $country_comment = '';
  public $language_comment = '';

  public $language_name = ''; // name of the language
  public $intl_language_name = ''; // internationalized name of the language
  public $language_direction = 'ltr';

  protected $locale_spec;

  function __construct($name, $config = null)
  {
    $this->locale_spec = new lmbLocaleSpec($name);
    if($config)
      $this->initLocaleSettings($config);
  }

  function initLocaleSettings($config)
  {
    if(!$config instanceof lmbIni)
      throw new lmbException('Config object must be an lmbIni instance', array('config' => $config));

    $this->_initCountrySettings($config);
    $this->_initLanguageSettings($config);
  }

  function isValid()
  {
    return $this->is_valid;
  }

  protected function _initCountrySettings($config)
  {
    $config->assignOption($this->time_format, 'time_format', 'date_time');
    $config->assignOption($this->short_time_format, 'short_time_format', 'date_time');
    $config->assignOption($this->date_format, 'date_format', 'date_time');
    $config->assignOption($this->short_date_format, 'short_date_format', 'date_time');
    $config->assignOption($this->date_time_format, 'date_time_format', 'date_time');
    $config->assignOption($this->short_date_time_format, 'short_date_time_format', 'date_time');
    $config->assignOption($this->short_date_short_time_format, 'short_date_short_time_format', 'date_time');

    if($config->hasOption('is_monday_first', 'date_time'))
      $this->is_monday_first = strtolower($config->getOption('is_monday_first', 'date_time')) == 'yes';

    if($this->is_monday_first)
      $this->week_days = array(1, 2, 3, 4, 5, 6, 0);
    else
      $this->week_days = array(0, 1, 2, 3, 4, 5, 6);

    $config->assignOption($this->country, 'country', 'regional_settings');
    $config->assignOption($this->country_comment, 'country_comment', 'regional_settings');

    $config->assignOption($this->decimal_symbol, 'decimal_symbol', 'numbers');
    $config->assignOption($this->thousand_separator, 'thousands_separator', 'numbers');
    $config->assignOption($this->fract_digits, 'fract_digits', 'numbers');
    $config->assignOption($this->negative_symbol, 'negative_symbol', 'numbers');
    $config->assignOption($this->positive_symbol, 'positive_symbol', 'numbers');

    $config->assignOption($this->currency_decimal_symbol, 'decimal_symbol', 'currency');
    $config->assignOption($this->currency_name, 'name', 'currency');
    $config->assignOption($this->currency_short_name, 'short_name', 'currency');
    $config->assignOption($this->currency_thousand_separator, 'thousands_separator', 'currency');
    $config->assignOption($this->currency_fract_digits, 'fract_digits', 'currency');
    $config->assignOption($this->currency_negative_symbol, 'negative_symbol', 'currency');
    $config->assignOption($this->currency_positive_symbol, 'positive_symbol', 'currency');
    $config->assignOption($this->currency_symbol, 'symbol', 'currency');
    $config->assignOption($this->currency_positive_format, 'positive_format', 'currency');
    $config->assignOption($this->currency_negative_format, 'negative_format', 'currency');
  }

  protected function _initLanguageSettings($config)
  {
    $config->assignOption($this->language_name, 'language_name', 'regional_settings');
    $config->assignOption($this->intl_language_name, 'international_language_name', 'regional_settings');
    $config->assignOption($this->language_comment, 'language_comment', 'regional_settings');
    $config->assignOption($this->language_direction, 'language_direction', 'regional_settings');
    $config->assignOption($this->LC_ALL, 'LC_ALL', 'regional_settings');

    $charset = false;
    if($config->hasOption('preferred', 'charset'))
    {
      $charset = $config->getOption('preferred', 'charset');
      if($charset != '')
        $this->charset = $charset;
    }

    if(!is_array($this->short_day_names))
      $this->short_day_names = array();
    if(!is_array($this->long_day_names))
      $this->long_day_names = array();

    foreach ($this->week_days as $day)
    {
      if($config->hasOption($day, 'short_day_names'))
        $this->short_day_names[$day] = $config->getOption($day, 'short_day_names');
      if($config->hasOption($day, 'long_day_names'))
        $this->long_day_names[$day] = $config->getOption($day, 'long_day_names');
    }

    if(!is_array($this->short_month_names))
      $this->short_month_names = array();
    if(!is_array($this->long_month_names))
      $this->long_month_names = array();

    foreach ($this->months as $month)
    {
      if($config->hasOption($month, 'short_month_names'))
        $this->short_month_names[$month] = $config->getOption($month, 'short_month_names');
      if($config->hasOption($month, 'long_month_names'))
        $this->long_month_names[$month] = $config->getOption($month, 'long_month_names');
    }

    if(!is_array($this->short_day_names))
      $this->short_day_names = array();
    if(!is_array($this->long_day_names))
      $this->long_day_names = array();

    foreach($this->week_days as $wday)
    {
      if($config->hasOption($wday, 'short_day_names'))
        $this->short_day_names[$wday] = $config->getOption($wday, 'short_day_names');
      if($config->hasOption($wday, 'long_day_names'))
        $this->long_day_names[$wday] = $config->getOption($wday, 'long_day_names');
    }
  }

  function getLocaleSpec()
  {
    return $this->locale_spec;
  }

  function getLocaleString()
  {
    return $this->locale_spec->getLocaleString();
  }

  function getLanguage()
  {
    return $this->locale_spec->getLanguage();
  }

  function setPHPLocale()
  {
    setlocale(LC_ALL, $this->LC_ALL);
  }

  function getCharset()
  {
    return $this->charset;
  }

  function getLanguageDirection()
  {
    return $this->language_direction;
  }

  function getCountryName()
  {
    return $this->country;
  }

  function getCountryComment()
  {
    return $this->country_comment;
  }

  function getLanguageComment()
  {
    return $this->language_comment;
  }

  function getLanguageName()
  {
    return $this->language_name;
  }

  function getIntlLanguageName()
  {
    return $this->intl_language_name;
  }

  function getCurrencySymbol()
  {
    return $this->currency_symbol;
  }

  function getCurrencyName()
  {
    return $this->currency_name;
  }

  function getCurrencyShortName()
  {
    return $this->currency_short_name;
  }

  function getTimeFormat()
  {
    return $this->time_format;
  }

  function getShortTimeFormat()
  {
    return $this->short_time_format;
  }

  function getDateFormat()
  {
    return $this->date_format;
  }

  function getShortDateFormat()
  {
    return $this->short_date_format;
  }

  function getShortDateTimeFormat()
  {
    return $this->short_date_time_format;
  }

  function getShortDateShortTimeFormat()
  {
    return $this->short_date_short_time_format;
  }

  function getDateTimeFormat()
  {
    return $this->date_time_format;
  }

  function isMondayFirst()
  {
    return $this->is_monday_first;
  }

  function getWeekDays()
  {
    return $this->week_days;
  }

  function getMonths()
  {
    return $this->months;
  }

  function getWeekDayNames($short = false)
  {
    if($short)
      return $this->short_day_names;
    else
      return $this->long_day_names;
  }

  function getMonthNames($short = false)
  {
    if($short)
      return $this->short_month_names;
    else
      return $this->long_month_names;
  }

  function getMeridiemName($hour)
  {
    return ($hour < 12) ? $this->am_name : $this->pm_name;
  }

  function getPmName()
  {
    return $this->pm_name;
  }

  function getAmName()
  {
    return $this->am_name;
  }

  function getDayName($num, $short = false)
  {
    if($num < 0 || $num > 6)
      return null;

    if($short)
      return $this->short_day_names[$num];
    else
      return $this->long_day_names[$num];
  }

  function getMonthName($num, $short = false)
  {
    if($num < 0 || $num > 11)
      return null;

    if($short)
      return $this->short_month_names[$num];
    else
      return $this->long_month_names[$num];
  }
}

