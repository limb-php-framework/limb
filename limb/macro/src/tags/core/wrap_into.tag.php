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
 * @tag wrap:into
 * @aliases into, wrap_into
 * @package macro
 * @version $Id$
 */
class lmbMacroWrapIntoTag extends lmbMacroTag
{
  protected $is_dynamic;
  
  function preParse($compiler)
  {
    parent :: preParse($compiler);
    
    if($wrapper = $this->findParentByClass('lmbMacroWrapTag'))
    {
      $this->is_dynamic = $wrapper->isDynamicWrap();  
    }
    else
    {
      $wrapper = $this->findRoot();
      $this->is_dynamic = false;  
    }
    
    if(!$this->is_dynamic)
    {
      $tree_builder = $compiler->getTreeBuilder();
      $this->_insert($wrapper, $tree_builder, $this->get('slot'));
    }
  }

  function _insert($wrapper, $tree_builder, $point)
  {
    $insertionPoint = $wrapper->findChild($point);
    if(empty($insertionPoint))
    {
      $params = array('slot' => $point);
      if($wrapper !== $this)
      {
        $params['parent_wrap_tag_file'] = $wrapper->getTemplateFile();
        $params['parent_wrap_tag_line'] = $wrapper->getTemplateLine();
      }

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

