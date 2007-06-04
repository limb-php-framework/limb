<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: constant.filter.php 5933 2007-06-04 13:06:23Z pachanga $
 * @package    $package$
 */

/**
* @filter const
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

?>