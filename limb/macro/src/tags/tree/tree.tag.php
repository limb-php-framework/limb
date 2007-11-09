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
 * @tag tree
 * @package macro
 * @version $Id$
 */
class lmbMacroTreeTag extends lmbMacroTag
{
  function generateContents($code)
  {
    if(!$level = $this->get('level'))
      $level = '$level';

    if(!$as = $this->get('as'))
      $as = '$item';

    if(!$kids_prop = $this->get('kids_prop'))
      $kids_prop = 'kids';

    $before_branch = $this->_getTagsBeforeBranch();
    $after_branch = $this->_getTagsAfterBranch();
    $branch = $this->findImmediateChildByClass('lmbMacroTreeBranchTag');

    $tree = $this->get('using');

    $items = $code->generateVar();
    $counter = $code->generateVar();

    $method = $code->beginMethod('_render_tree'. uniqid(), array($items, $level));
    $code->writePHP($counter . '=0;');

    $code->writePHP('foreach(' . $items . ' as ' . $as . ') {');

    //rendering tags before branch
    $code->writePHP('if(!' . $counter . ') {');
    foreach($before_branch as $tag)
      $tag->generateContents($code);
    $code->writePHP('}');

    $branch->setRecursionMethod($method);
    $branch->generateContents($code);

    $code->writePHP($counter . '++;');
    $code->writePHP('}');//foreach

    //rendering tags after branch
    $code->writePHP('if(' . $counter . ') {');
    foreach($after_branch as $tag)
      $tag->generateContents($code);
    $code->writePHP('}');

    $code->endMethod();

    $code->writePHP('$this->' . $method . '(' . $tree . ', 0);');
  }

  protected function _getTagsBeforeBranch()
  {
    $tags = array();
    foreach($this->children as $child)
    {
      if(is_a($child, 'lmbMacroTreeBranchTag'))
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
      if(is_a($child, 'lmbMacroTreeBranchTag'))
        $collect = true;
    }
    return $tags;
  }
}

