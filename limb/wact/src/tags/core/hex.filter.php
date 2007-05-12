<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: hex.filter.php 5873 2007-05-12 17:17:45Z serega $
 * @package    wact
 */

/**
 * @filter hex
 */
class WactHexFilter extends WactCompilerFilter
{
  function getValue()
  {
    if ($this->isConstant())
      return str_replace('&#x;', '', preg_replace("/(.)*/Uimse", "'&#x'.bin2hex('\\1').';'", $this->base->getValue()));
    else
      $this->raiseUnresolvedBindingError();
  }

  function generateExpression($code_writer)
  {
    $code_writer->writePHP('str_replace(\'&#x;\', \'\', preg_replace("/(.)*/Uimse", "\'&#x\'.bin2hex(\'\\\\1\').\';\'", ');
    $this->base->generateExpression($code_writer);
    $code_writer->writePHP('))');
  }
}

?>
