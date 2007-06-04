<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: nl2br.filter.php 5933 2007-06-04 13:06:23Z pachanga $
 * @package    $package$
 */

/**
 * @filter nl2br
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
?>
