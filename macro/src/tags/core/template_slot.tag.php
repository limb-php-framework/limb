<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * class lmbMacroTemplateSlotTag.
 *  Very simple placeholder for {{apply:into}} tag
 * @tag template:slot
 * @package macro
 * @version $Id$
 */
class lmbMacroTemplateSlotTag extends lmbMacroTag
{
  protected function _generateContent($code)
  {
    $parent_template_tag = $this->findParentByClass('lmbMacroTemplateTag');
    $apply_tag = $parent_template_tag->getCurrentApplyTag();
    
    $intos = $apply_tag->findChildrenByClass('lmbMacroApplyIntoTag');
    foreach($intos as $into)
    {
      if($into->get('slot') == $this->getNodeId())
        $into->generateNow($code);
    }
  }
}

