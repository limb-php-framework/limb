<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
/**
 * @filter i18n_number
 * @max_attributes 5
 * @package i18n
 * @version $Id: number.filter.php 7486 2009-01-26 19:13:20Z pachanga $
 */
class lmbI18NNumberFilter extends WactCompilerFilter
{
  var $locale_var;

  function getValue()
  {
    $value = $this->base->getValue();

    $toolkit = lmbToolkit :: instance();

    if(isset($this->parameters[0]) && $this->parameters[0]->getValue())
      $locale = $toolkit->getLocaleObject($this->parameters[0]->getValue());
    else
      $locale = $toolkit->getLocaleObject();

    if(isset($this->parameters[1]) && $this->parameters[1]->getValue())
      $fract_digits = $this->parameters[1]->getValue();
    else
      $fract_digits = $locale->fract_digits;

    if(isset($this->parameters[2]) && $this->parameters[2]->getValue())
      $decimal_symbol = $this->parameters[2]->getValue();
    else
      $decimal_symbol = $locale->decimal_symbol;

    if(isset($this->parameters[3]) && $this->parameters[3]->getValue())
      $thousand_separator = $this->parameters[3]->getValue();
    else
      $thousand_separator = $locale->thousand_separator;

    if ($this->isConstant())
      return number_format($value, $fract_digits, $decimal_symbol, $thousand_separator);
    else
      $this->raiseUnresolvedBindingError();
  }

  function generatePreStatement($code)
  {
    $toolkit_var = $code->getTempVarRef();
    $this->locale_var = $code->getTempVarRef();

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
  }

  function generateExpression($code)
  {
    $code->writePHP('number_format(');
    $this->base->generateExpression($code);
    $code->writePHP(',');

    if(isset($this->parameters[1]) && $this->parameters[1]->getValue())
      $this->parameters[1]->generateExpression($code);
    else
      $code->writePHP($this->locale_var . '->fract_digits');

    $code->writePHP(',');

    if(isset($this->parameters[2]) && $this->parameters[2]->getValue())
      $this->parameters[2]->generateExpression($code);
    else
      $code->writePHP($this->locale_var . '->decimal_symbol');

    $code->writePHP(',');

    if(isset($this->parameters[3]) && $this->parameters[3]->getValue())
      $this->parameters[3]->generateExpression($code);
    else
      $code->writePHP($this->locale_var . '->thousand_separator');

    $code->writePHP(')');
  }
}


