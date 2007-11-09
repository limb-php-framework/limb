<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * @tag pager:number
 * @package macro
 * @version $Id$
 */
class lmbMacroPagerNumberTag extends lmbMacroTag
{
  function generate($code)
  {
    $pager = $this->findParentByClass('lmbMacroPagerTag')->getPagerVar();
    
    $code->writePhp("if (!{$pager}->isDisplayedPage()) {\n");

    $code->writePhp("\$href = {$pager}->getPageUri();\n");
    $code->writePhp("\$number = {$pager}->getPage();\n");

    parent :: generate($code);

    $code->writePhp("}\n");
  }
}


