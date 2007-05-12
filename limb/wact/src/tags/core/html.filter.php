<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: html.filter.php 5873 2007-05-12 17:17:45Z serega $
 * @package    wact
 */

/**
 * @filter html
 * @version $Id: html.filter.php 5873 2007-05-12 17:17:45Z serega $
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
