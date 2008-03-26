<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

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
    $code->registerInclude('limb/i18n/src/macro/filters.inc.php');
    parent :: preGenerate($code);
  }
  function getValue()
  {
    $params="array(";
    foreach ($this->params as $key=>$value)
    {
      $params.=$value.",";
    }
    $params.=")";
    return 'lmb_i18n_date_filter(' . $params.', ' . $this->base->getValue() . ')';
  }
}