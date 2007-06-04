<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: safejstext.filter.php 5933 2007-06-04 13:06:23Z pachanga $
 * @package    $package$
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