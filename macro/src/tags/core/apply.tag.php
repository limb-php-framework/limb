<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
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
      
    //experimental support for dynamic apply tags
    if($this->isDynamic('template'))
    {
      if($this->getBool('inline'))
        $this->raise('Inline is not supported for dynamic selector');      

      //in case of dynamic we have to generate all possible template tags
      //and we need to do this only once
      static $template_tags_generated;

      if(!isset($template_tags_generated))
      {
        $root = $this->findRoot();
        $tags = $root->findChildrenByClass('lmbMacroTemplateTag');
        foreach($tags as $tag)
          $tag->generateFromDynamicAppply($code);
        $template_tags_generated = true;
      }

      $method = $code->generateVar();
      $code->writePHP('if(!isset(' . '$this->__template_tags[' . $name . ']))' . "\n" . 
                      ' throw new lmbMacroException("Could not find template tag \'" . ' . $name . ' . "\' for dynamic apply");' . "\n"
                      );
      $code->writePHP($method . ' = $this->__template_tags[' . $name . '];');
      $code->writePHP('$this->' . $method . '(' . $arg_str . ');');
    }
    else
    {
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
  }
  
  function findTemplateTagNode()
  {
    $name = $this->get('template');
    return $this->findUpChild('template_' . $name);
  }
}

