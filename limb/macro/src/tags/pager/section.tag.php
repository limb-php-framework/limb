<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * @tag pager:section
 * @restrict_self_nesting
 * @parent_tag_class lmbMacroPagerListTag
 * @package macro
 * @version $Id$
 */
class lmbMacroPagerSectionTag extends lmbMacroTag
{
  protected function _generateContent($code)
  {
    $pager = $this->findParentByClass('lmbMacroPagerTag')->getRuntimeVar();
    
    $code->writePhp("if (!{$pager}->isDisplayedSection()) {\n");

    $code->writePhp("\$href = {$pager}->getSectionUri();\n");
    $code->writePhp("\$section_begin_page = {$pager}->getSectionBeginPage();\n");
    $code->writePhp("\$section_end_page = {$pager}->getSectionEndPage();\n");

    parent :: _generateContent($code);

    $code->writePhp("}\n");
  }
}


