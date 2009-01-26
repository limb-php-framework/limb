<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

require_once('limb/i18n/src/datetime/lmbLocaleDateTime.class.php');
require_once('limb/datetime/src/lmbDateTime.class.php');

function lmb_i18n_date_filter($params,$value)
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
    if(isset($params[2]) && $params[2])
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
