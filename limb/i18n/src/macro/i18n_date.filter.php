<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

lmb_require('limb/i18n/src/datetime/lmbLocaleDateTime.class.php');

/**
 * Filter i18n_date for macro templates
 * @filter i18n_date
 * @package i18n
 * @version $Id$
 */
class lmbI18NMacroDateFilter extends lmbMacroFilter
{
  var $date;

  function preGenerate($code)
  {
    $code->registerInclude('limb/i18n/src/datetime/lmbLocaleDateTime.class.php');
    $code->registerInclude('limb/datetime/src/lmbDateTime.class.php');
    parent :: preGenerate($code);
  }
  function getValue()
  {
    $params="array(";
    foreach ($this->params as $key=>$value)
    {
    $params.=")";
    return 'lmbI18NMacroDateFilter::_i18nDateFilter(' . $params.', ' . $this->base->getValue() . ')';
  }

  static function _i18nDateFilter($params,$value)
  {
    $toolkit = lmbToolkit :: instance();
    if(isset($params[0]) && $params[0])
    {
      $locale=$toolkit->getLocaleObject($params[0]);
    }
    else
      $locale=$toolkit->getLocaleObject();

    if(isset($params[3]) && $params[3])
      $format=$params[3];
    else
    {
        $format_type = $params[2];
      else
        $format_type = 'short_date';

      $property = $format_type . '_format';
      $format=$locale->$property;
    }

    if(isset($params[1]) && $params[1])
      $date_type = $params[1];
    else
      $date_type = 'stamp';

    switch($date_type)
    {
      case 'string': $date = new lmbLocaleDateTime($value);      break;
      case 'stamp':  $date = new lmbLocaleDateTime((int)$value); break;
      default:       $date = new lmbLocaleDateTime($value);      break;
    }

    return $date->localeStrftime($format, $locale);
  }
}
