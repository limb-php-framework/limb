<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * @tag tree
 * @package macro
 * @version $Id$
 */
class lmbMacroTreeTag extends lmbMacroTag
{
  protected function _generateContent($code)
  {
    if(!$level = $this->get('level'))
      $level = '$level';

    if(!$as = $this->get('as'))
      $as = '$item';

    if(!$kids_prop = $this->get('kids_prop'))
      $kids_prop = 'kids';

    $before_branch = $this->_getTagsBeforeBranch();
    $after_branch = $this->_getTagsAfterBranch();
    $branch = $this->findImmediateChildByClass('lmbMacroTreeItemTag');

    $tree = $this->get('using');

    $items = $code->generateVar();
    $counter = $code->generateVar();
    $extra_params = $code->generateVar(); 

    $this->method = $code->beginMethod('_render_tree'. uniqid(), array($items, $level, $extra_params . '= array()'));
    
    $code->writePHP("if($extra_params) extract($extra_params);"); 

    $code->writePHP($counter . "=0;\n");

    $code->writePHP('foreach(' . $items . ' as ' . $as . ") {\n");

    if($user_counter = $this->get('counter'))
      $code->writePHP($user_counter . ' = ' . $counter . "+1;\n");
     
    //rendering tags before branch
    $code->writePHP('if(!' . $counter . ") {\n");
    foreach($before_branch as $tag)
      $tag->generate($code);
    $code->writePHP("}\n");

    $branch->generate($code);

    $code->writePHP($counter . "++;\n");
    $code->writePHP("}\n");//foreach

    //rendering tags after branch
    $code->writePHP('if(' . $counter . ") {\n");
    foreach($after_branch as $tag)
      $tag->generate($code);
    $code->writePHP("}\n");

    $code->endMethod();

    $arg_str = $this->extraAttributesIntoArrayString();
    $code->writePHP('$this->' . $this->method . '(' . $tree . ', 0' . ',' . $arg_str . ");\n");
  }
  
  function getRecursionMethod()
  {
    return $this->method;
  }

  protected function _getTagsBeforeBranch()
  {
    $tags = array();
    foreach($this->children as $child)
    {
      if(is_a($child, 'lmbMacroTreeItemTag'))
        break;
      $tags[] = $child;
    }
    return $tags;
  }

  protected function _getTagsAfterBranch()
  {
    $tags = array();
    $collect = false;
    foreach($this->children as $child)
    {
      if($collect)
        $tags[] = $child;
      if(is_a($child, 'lmbMacroTreeItemTag'))
        $collect = true;
    }
    return $tags;
  }
  
  function extraAttributesIntoArrayString()
  {
    return $this->attributesIntoArrayString($skip = array('as', 'using', 'counter', 'level'));
  }
}

