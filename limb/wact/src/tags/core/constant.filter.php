<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: constant.filter.php 5168 2007-02-28 16:05:08Z serega $
 * @package    wact
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