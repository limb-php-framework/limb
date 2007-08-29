<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * @filter const
 * @package wact
 * @version $Id: constant.filter.php 6243 2007-08-29 11:53:10Z pachanga $
 */
class WactConstantFilter extends WactCompilerFilter
{
  function getValue()
  {
    return constant($this->base->getValue());
  }

  function generateExpression($code_writer)
  {
    $code_writer->writePHP('@constant(');
    $this->base->generateExpression($code_writer);
    $code_writer->writePHP(')');
  }
}


