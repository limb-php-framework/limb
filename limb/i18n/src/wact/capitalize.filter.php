<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
/**
 * @filter i18n_capitalize
 * @package i18n
 * @version $Id: capitalize.filter.php 6721 2008-01-22 08:39:51Z serega $
 */
class lmbI18NCapitalizeFilter extends WactCompilerFilter
{
  var $locale_var;

  function getValue()
  {
    $value = $this->base->getValue();

    $toolkit = lmbToolkit :: instance();

    if ($this->isConstant())
      return lmb_ucfirst($value);
    else
      $this->raiseUnresolvedBindingError();
  }

  function generateExpression($code)
  {
    parent :: generateExpression($code);

    $code->writePHP('lmb_ucfirst(');
    $this->base->generateExpression($code);
    $code->writePHP(')');
  }
}


