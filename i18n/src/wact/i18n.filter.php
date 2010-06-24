<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
/**
 * @filter i18n
 * @min_attributes 1
 * @max_attributes 100
 * @package i18n
 * @version $Id: i18n.filter.php 7486 2009-01-26 19:13:20Z pachanga $
 */
class lmbI18NStringFilter extends WactCompilerFilter
{
  function getValue()
  {
    if(!isset($this->parameters[0]) || !$this->parameters[0]->getValue())
      throw new WactException('MISSING_FILTER_PARAMETER');
    else
      $domain = $this->parameters[0]->getValue();

    $value = $this->base->getValue();

    if($this->isConstant())
      return lmb_i18n($value, $this->_getAttributes(), $domain);
    else
      $this->raiseUnresolvedBindingError();
  }

  function _getAttributes()
  {
    $result = array();

    for($i=1; $i < sizeof($this->parameters); $i+=2)
    {
      $var = $this->parameters[$i]->getValue();
      $value = $this->parameters[$i+1]->getValue();
      $result[$var] = $value;
    }

    return $result;
  }

  function generatePreStatement($code)
  {
    parent :: generatePreStatement($code);

    $this->params_var = $code->getTempVarRef();
    $code->writePhp($this->params_var . ' = array();');

    for($i=1; $i < sizeof($this->parameters); $i+=2)
    {
      $var = $this->parameters[$i]->getValue();
      $code->writePhp($this->params_var . '["' . $this->parameters[$i]->getValue() . '"] = ');
      $code->writePhp($this->parameters[$i+1]->generateExpression($code));
      $code->writePhp(';'. "\n");
    }
  }

  function generateExpression($code)
  {
    $code->writePhp('lmb_i18n(');

    $this->base->generateExpression($code);

    $code->writePhp(',' . $this->params_var . ', ');

    $code->writePhp($this->parameters[0]->generateExpression($code));
    $code->writePhp(')');
  }
}


