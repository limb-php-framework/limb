<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * This filter replaces all new lines with <br/> tag.
 * Could be usefull with java script sometimes
 * @filter safe_js_text
 * @package wact
 * @version $Id: safejstext.filter.php 7686 2009-03-04 19:57:12Z korchasa $
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


