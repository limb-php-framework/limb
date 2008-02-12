<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * @tag apply
 * @req_attributes template
 * @package macro
 * @version $Id$
 */
class lmbMacroApplyTag extends lmbMacroTag
{
  protected function _generateContent($code)
  {
    $name = $this->get('template');

    $arg_str = $this->attributesIntoArrayString();
    
    if(!$template_tag_node = $this->findTemplateTagNode())
      $this->raise('Template tag not found', array('template' => $name));
    
    $template_tag_node->setCurrentApplyTag($this);

    if($this->getBool('inline'))
      $template_tag_node->generateNow($code, $wrap_with_method = false);
    else
    {
      $template_tag_node->generateNow($code);
      $code->writePHP('$this->' . $template_tag_node->getMethod() . '(' . $arg_str . ');');
    }
  }
  
  function findTemplateTagNode()
  {
    $name = $this->get('template');
    return $this->findUpChild('template_' . $name);
  }
}

