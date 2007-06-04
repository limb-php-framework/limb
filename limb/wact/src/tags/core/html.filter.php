<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: html.filter.php 5933 2007-06-04 13:06:23Z pachanga $
 * @package    $package$
 */

/**
 * @filter html
 * @version $Id: html.filter.php 5933 2007-06-04 13:06:23Z pachanga $
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
