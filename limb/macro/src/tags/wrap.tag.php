<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

//temporary includes, make it more flexible later
lmb_require('limb/macro/src/lmbMacroTagDictionary.class.php');
lmb_require('limb/macro/src/lmbMacroTagInfo.class.php');
lmb_require('limb/macro/src/lmbMacroTag.class.php');

lmbMacroTagDictionary :: instance()->register(new lmbMacroTagInfo('wrap', 'lmbMacroWrapTag', false), __FILE__);

/**
 * class lmbMacroWrapTag.
 *
 * @package macro
 * @version $Id$
 */
class lmbMacroWrapTag extends lmbMacroTag
{
  function preParse($compiler)
  {
    parent :: preParse($compiler);

    $tree_builder = $compiler->getTreeBuilder();

    $file = $this->get('with');
    $this->_compileSourceFileName($file, $compiler);

    //if there's no 'into' attribute we consider that <%into%> tags used instead
    if($into = $this->get('into'))
      $this->_insert($this, $tree_builder, $into);
  }

  protected function _compileSourceFileName($file, $compiler)
  {
    $this->sourcefile = $compiler->getTemplateLocator()->locateSourceTemplate($file);

    if(empty($this->sourcefile))
      $this->raise('Template source file not found', array('file_name' => $file));

    $compiler->parseTemplate($file, $this);
  }

  function _insert($wrapper, $tree_builder, $point)
  {
    $this->_insertOrReplace($wrapper, $tree_builder, $point, $replace = false);
  }

  protected function _insertOrReplace($wrapper, $tree_builder, $point, $replace = false)
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

    if($replace)
      $insertionPoint->removeChildren();

    $tree_builder->pushCursor($insertionPoint, $this->location);
  }
}

