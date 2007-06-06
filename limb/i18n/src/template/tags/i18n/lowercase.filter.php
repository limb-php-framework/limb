<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
/**
 * @filter i18n_lowercase
 * @package i18n
 * @version $Id: lowercase.filter.php 5945 2007-06-06 08:31:43Z pachanga $
 */
class lmbI18NLowercaseFilter extends WactCompilerFilter
{
  var $locale_var;

  function getValue()
  {
    $value = $this->base->getValue();

    $toolkit = lmbToolkit :: instance();

    if ($this->isConstant())
      return lmb_strtolower($value);
    else
      $this->raiseUnresolvedBindingError();
  }

  function generateExpression($code)
  {
    $code->writePHP('lmb_strtolower(');
    $this->base->generateExpression($code);
    $code->writePHP(')');
  }
}

?>