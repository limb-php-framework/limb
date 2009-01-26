<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * @tag pager:next
 * @parent_tag_class lmbMacroPagerTag
 * @restrict_self_nesting
 * @package macro
 * @version $Id$
 */
class lmbMacroPagerNextTag extends lmbMacroTag
{
  protected function _generateContent($code)
  {
    $pager = $this->findParentByClass('lmbMacroPagerTag')->getRuntimeVar();
    
    $code->writePhp("if ({$pager}->hasNext()) {\n");
    $code->writePhp("\$href = {$pager}->getPageUri({$pager}->getCurrentPage() + 1 );\n");

    parent :: _generateContent($code);

    $code->writePhp("}\n");
  }
}


