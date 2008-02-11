<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * class lmbMacroIntoTag.
 *
 * @tag include:into
 * @aliases include_into
 * @package macro
 * @version $Id$
 */
class lmbMacroIncludeIntoTag extends lmbMacroTag
{
  protected $is_dynamic;
  
  function preParse($compiler)
  {
    parent :: preParse($compiler);
    
    if($parent = $this->findParentByClass('lmbMacroIncludeTag'))
      $this->is_dynamic = $parent->isDynamicInclude();  
    else
    {
      $parent = $this->findRoot();
      $this->is_dynamic = false;  
    }
    
    if(!$this->is_dynamic)
    {
      $tree_builder = $compiler->getTreeBuilder();
      $this->_insert($parent, $tree_builder, $this->get('slot'));
    }
  }

  function _insert($parent, $tree_builder, $point)
  {
    $insertionPoint = $parent->findChild($point);
    if(empty($insertionPoint))
    {
      $params = array('slot' => $point);
      $params['parent_wrap_tag_file'] = $parent->getTemplateFile();
      $params['parent_wrap_tag_line'] = $parent->getTemplateLine();

      $this->raise('Wrap slot not found', $params);
    }

    $tree_builder->pushCursor($insertionPoint, $this->location);
  }
  
  function generate($code)
  {
    if(!$this->is_dynamic)
      parent :: generate($code);
  }
  
  function generateNow($code)
  {
    parent :: generate($code); 
  }
}

