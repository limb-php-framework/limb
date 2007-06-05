<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * @filter html
 * @version $Id: html.filter.php 5942 2007-06-05 19:22:26Z pachanga $
 */
class WactHtmlFilter extends WactCompilerFilter
{
  function getValue() {
    if ($this->isConstant())
      return htmlspecialchars($this->base->getValue(), ENT_QUOTES);
    else
      $this->raiseUnresolvedBindingError();
  }

  function generateExpression($code_writer)
  {
    $code_writer->writePHP('htmlspecialchars(');
    $this->base->generateExpression($code_writer);
    $code_writer->writePHP(', ENT_QUOTES)');
  }
}

?>
