<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * @filter default
 * @min_attributes 1
 * @max_attributes 1
 * @version $Id: default.filter.php 7686 2009-03-04 19:57:12Z korchasa $
 * @package wact
 */
class WactDefaultFilter extends WactCompilerFilter
{
  function getValue()
  {
    if ($this->isConstant())
    {
      $value = $this->base->getValue();
      if (empty($value) && $value !== "0" && $value !== 0)
      {
        return $this->parameters[0]->getValue();
      } else {
        return $value;
      }
    }
    else
      $this->raiseUnresolvedBindingError();
  }

  function generateExpression($code_writer)
  {
    $code_writer->registerInclude('limb/wact/src/components/core/default_filter.inc.php');
    $code_writer->writePHP('WactApplyDefault(');
    $this->base->generateExpression($code_writer);
    $code_writer->writePHP(',');
    $this->parameters[0]->generateExpression($code_writer);
    $code_writer->writePHP(')');
  }
}


