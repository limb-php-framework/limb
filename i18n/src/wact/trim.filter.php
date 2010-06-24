<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
/**
 * @filter i18n_trim
 * @max_attributes 1
 * @package i18n
 * @version $Id: trim.filter.php 7486 2009-01-26 19:13:20Z pachanga $
 */
class lmbI18NTrimFilter extends WactCompilerFilter
{
  function getValue()
  {
   if(isset($this->parameters[0]) && $this->parameters[0]->getValue())
      $characters = $this->parameters[0]->getValue();
    else
      $characters = '';

    if (!$this->isConstant())
      $this->raiseUnresolvedBindingError();

    if($characters)
      return lmb_trim($this->base->getValue(), $characters);
    else
      return lmb_trim($this->base->getValue());
  }

  function generateExpression($code)
  {
    $code->writePHP('lmb_trim(');
    $this->base->generateExpression($code);

    if(isset($this->parameters[0]) && $this->parameters[0]->getValue())
    {
      $code->writePHP(',');
      $this->parameters[0]->generateExpression($code);
    }

    $code->writePHP(')');
  }
}


