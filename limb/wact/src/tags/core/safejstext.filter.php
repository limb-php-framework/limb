<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: safejstext.filter.php 5021 2007-02-12 13:04:07Z pachanga $
 * @package    wact
 */

/**
* This filter replaces all new lines with <br/> tag.
* Could be usefull with java script sometimes
* @filter safe_js_text
*/
class WactSafeJsTextFilter extends WactCompilerFilter
{
  function getValue()
  {
    if ($this->isConstant())
      return preg_replace("/\r\n|\r/", "<br/>", $this->base->getValue());
    else
      $this->raiseUnresolvedBindingError();
  }

  function generateExpression($code)
  {
    $code->writePHP('preg_replace("/\r\n|\r/", "<br/>", ');
    $this->base->generateExpression($code);
    $code->writePHP(')');
  }
}

?>