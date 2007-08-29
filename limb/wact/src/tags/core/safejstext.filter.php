<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * This filter replaces all new lines with <br/> tag.
 * Could be usefull with java script sometimes
 * @filter safe_js_text
 * @package wact
 * @version $Id: safejstext.filter.php 6243 2007-08-29 11:53:10Z pachanga $
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


