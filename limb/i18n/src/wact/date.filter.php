<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/i18n/src/datetime/lmbLocaleDateTime.class.php');

/**
 * @filter i18n_date
 * @max_attributes 5
 * @package i18n
 * @version $Id: date.filter.php 7486 2009-01-26 19:13:20Z pachanga $
 */
class lmbI18NDateFilter extends WactCompilerFilter
{
  var $date;

  var $locale_var;
  var $date_var;
  var $date_format_var;

  function getValue()
  {
    $value = $this->base->getValue();

    $toolkit = lmbToolkit :: instance();

    if(isset($this->parameters[0]) && $this->parameters[0]->getValue())
      $locale = $toolkit->getLocaleObject($this->parameters[0]->getValue());
    else
      $locale = $toolkit->getLocaleObject();

    $this->date = new lmbLocaleDateTime();

    $this->_setDate();

    if($this->isConstant())
      return $this->date->localeStrftime($this->_getFormat($locale), $locale);
    else
      $this->raiseUnresolvedBindingError();
  }

  function _setDate()
  {
    if(isset($this->parameters[1]) && $this->parameters[1]->getValue())
      $date_type = $this->parameters[1]->getValue();
    else
      $date_type = 'stamp';

    $value = $this->base->getValue();
    switch($date_type)
    {
      case 'string':
        $this->date = new lmbLocaleDateTime($value);
      break;
      case 'stamp':
        $this->date = new lmbLocaleDateTime((int)$value);
      break;

      default:
        $this->date = new lmbLocaleDateTime($value);
      break;
    }
  }

  function _getFormat($locale)
  {
    if(isset($this->parameters[3]) && $this->parameters[3]->getValue())
      return $this->parameters[3]->getValue();

    if(isset($this->parameters[2]) && $this->parameters[2]->getValue())
      $format_type = $this->parameters[2]->getValue();
    else
      $format_type = 'short_date';

    $property = $format_type . '_format';
    return $locale->$property;
  }

  function generatePreStatement($code)
  {
    parent :: generatePreStatement($code);

    $toolkit_var = $code->getTempVarRef();
    $this->locale_var = $code->getTempVarRef();

    $code->writePHP("lmb_require('limb/i18n/src/datetime/lmbLocaleDateTime.class.php');");
    $code->writePHP($toolkit_var . ' = lmbToolkit :: instance();' . "\n");
    $code->writePHP($this->locale_var . ' = ');

    if(isset($this->parameters[0]) && $this->parameters[0]->getValue())
    {
      $code->writePHP($toolkit_var . '->getLocaleObject("' . $this->parameters[0]->getValue(). '");');
    }
    else
    {
      $code->writePHP($toolkit_var . '->getLocaleObject();');
    }

    $this->date_var = $code->getTempVarRef();

    $this->_setDBEDate($code);

  }

  function _setDBEDate($code)
  {
    if(isset($this->parameters[1]) && $this->parameters[1]->getValue())
      $date_type = $this->parameters[1]->getValue();
    else
      $date_type = 'stamp';

    switch($date_type)
    {
      case 'stamp':
        $code->writePHP($this->date_var . ' = new lmbLocaleDateTime((int)');
        $this->base->generateExpression($code);
        $code->writePHP(');');
      break;

      case 'string':
        $code->writePHP($this->date_var . ' = new lmbLocaleDateTime(');
        $this->base->generateExpression($code);
        $code->writePHP(');');
      break;

      default:
        $code->writePHP($this->date_var . ' = new lmbLocaleDateTime((int)');
        $this->base->generateExpression($code);
        $code->writePHP(');');
      break;
    }
  }

  function generateExpression($code)
  {
    parent :: generateExpression($code);

    $code->writePHP($this->date_var . '->localeStrftime(');
    $this->_getDBEFormat($code);
    $code->writePHP(' ,' . $this->locale_var . ')');
  }

  function _getDBEFormat($code)
  {
    if(isset($this->parameters[3]) && $this->parameters[3]->getValue())
    {
      $code->writePHP('"' . $this->parameters[3]->getValue() . '"');
      return;
    }

    if(isset($this->parameters[2]) && $this->parameters[2]->getValue())
      $format_type = $this->parameters[2]->getValue();
    else
      $format_type = 'short_date';

    $property = $format_type . '_format';
    $code->writePHP($this->locale_var .'->' . $property);
  }
}

