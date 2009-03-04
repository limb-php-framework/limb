<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * Merges the current template with a wrapper template, the current
 * template being inserted into the wrapper at the point where the
 * wrap tag exists.
 * @tag core:WRAP
 * @package wact
 * @version $Id: wrap.tag.php 7686 2009-03-04 19:57:12Z korchasa $
 */
class WactCoreWrapTag extends WactRuntimeComponentTag
{
  protected $sourcefile;

  /**
  * @param WactCompiler
  **/
  function preParse($compiler)
  {
    $tree_builder = $compiler->getTreeBuilder();

    if($file = $this->getAttribute('file'))
    {
      $this->_compileSourceFileName($file, $compiler);

      $this->_makeInsertion($this, $tree_builder);
    }
    else
      $this->_makeInsertionIntoParent($tree_builder);
  }

  protected function _compileSourceFileName($file, $compiler)
  {
    $this->sourcefile = $compiler->getTemplateLocator()->locateSourceTemplate($file);

    if (empty($this->sourcefile))
       $this->raiseCompilerError('Template source file not found', array('file_name' => $file));

    $compiler->parseTemplate($file, $this);
  }

  protected function _makeInsertion($wrapper, $tree_builder, $raise_error = false)
  {
    if(($insertionId = $this->getAttribute('insertat')) || ($insertionId = $this->getAttribute('in')))
      $this->insert($wrapper, $tree_builder, $insertionId);
    else
    {
      if(($insertionId = $this->getAttribute('replaceat')) || ($insertionId = $this->getAttribute('as')))
        $this->replace($wrapper, $tree_builder, $insertionId);
      elseif($raise_error)
       $this->raiseRequiredAttributeError('insertat or replaceat(in or as)');
    }
  }

  protected function _makeInsertionIntoParent($tree_builder)
  {
    $parent = $this->findParentByClass('WactCoreWrapTag');
    if(!$parent || !$parent->sourcefile)
      $this->raiseRequiredAttributeError('file');

    $this->_makeInsertion($parent, $tree_builder, true, $this->location_in_template);
  }

  function insert($wrapper, $tree_builder, $point)
  {
    $this->_insertOrReplace($wrapper, $tree_builder, $point, $replace = false);
  }

  function replace($wrapper, $tree_builder, $point)
  {
    $this->_insertOrReplace($wrapper, $tree_builder, $point, $replace = true);
  }

  protected function _insertOrReplace($wrapper, $tree_builder, $point, $replace = false)
  {
    $insertionPoint = $wrapper->findChild($point);
    if (empty($insertionPoint))
    {
        $params = array('placeholder' => $point);
        if($wrapper !== $this)
        {
          $params['parent_wrap_tag_file'] = $wrapper->getTemplateFile();
          $params['parent_wrap_tag_line'] = $wrapper->getTemplateLine();
        }

        $this->raiseCompilerError('Wrap placeholder not found', $params);
    }

    if($replace)
      $insertionPoint->removeChildren();

    $tree_builder->pushCursor($insertionPoint, $this->location_in_template);
  }
}

