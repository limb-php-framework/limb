<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * @tag pager:current
 * @parent_tag_class lmbMacroPagerListTag
 * @restrict_self_nesting
 * @package macro
 * @version $Id$
 */
class lmbMacroPagerCurrentTag extends lmbMacroTag
{
  protected function _generateContent($code)
  {
    $pager = $this->findParentByClass('lmbMacroPagerTag')->getRuntimeVar();  

    $code->writePhp("if ({$pager}->isDisplayedPage()) {\n");

    $code->writePhp("\$href = {$pager}->getCurrentPageUri();\n");
    $code->writePhp("\$number = {$pager}->getPage();\n");

    parent :: _generateContent($code);

    $code->writePhp("}\n");
  }
}


