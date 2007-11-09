<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

lmb_require('limb/macro/src/lmbMacroTag.class.php');

/**
 * class lmbMacroWrapTag.
 *
 * @tag wrap
 * @package macro
 * @version $Id$
 */
class lmbMacroWrapTag extends lmbMacroTag
{
  protected $is_dynamic = false;

  function preParse($compiler)
  {
    parent :: preParse($compiler);

    if($this->isDynamic('with'))
      $this->is_dynamic = true;

    if(!$this->is_dynamic)
    {
      $file = $this->get('with');
      $this->_compileSourceFileName($file, $compiler);

      //if there's no 'into' attribute we consider that {{into}} tags used instead
      if($into = $this->get('into'))
      {
        $tree_builder = $compiler->getTreeBuilder();
        $this->_insert($this, $tree_builder, $into);
      }
    }
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

  function isDynamicWrap()
  {
    return $this->is_dynamic;
  }

  protected function _collectIntos()
  {
    return $this->findImmediateChildrenByClass('lmbMacroIntoTag');
  }

  function generateContents($code)
  {
    if($this->is_dynamic)
    {
      $handlers_str = 'array(';
      $methods = array();

      //collecting {{into}} tags
      if($intos = $this->_collectIntos())
      {
        foreach($intos as $into)
        {
          $methods[$into->get('slot')] = $code->beginMethod('__slotHandler'. uniqid());
          $into->generateContents($code);
          $code->endMethod();
        }
      }
      else
      {
        $methods[$this->get('into')] = $code->beginMethod('__slotHandler'. uniqid());
        parent :: generateContents($code);
        $code->endMethod();
      }

      foreach($methods as $slot => $method)
        $handlers_str .= '"' . $slot . '"' . ' => array($this, "' . $method . '"),';

      $handlers_str .= ')';

      $code->writePHP('$this->wrapTemplate(' . $this->getEscaped('with') . ', ' . $handlers_str . ');');
    }
    else
      parent :: generateContents($code);
  }
}

