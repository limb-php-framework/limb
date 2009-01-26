<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * @filter nl2br
 * @package wact
 * @version $Id: nl2br.filter.php 7486 2009-01-26 19:13:20Z pachanga $
 */
class WactNL2BRFilter extends WactCompilerFilter
{
  function getValue()
  {
    if ($this->isConstant())
      return nl2br($this->base->getValue());
    else
      $this->raiseUnresolvedBindingError();
  }

  function generateExpression($code_writer)
  {
    $code_writer->writePHP('nl2br(');
    $this->base->generateExpression($code_writer);
    $code_writer->writePHP(')');
  }
}

