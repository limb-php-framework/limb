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
 * @tag tree:branch
 * @package macro
 * @version $Id$
 */
class lmbMacroTreeBranchTag extends lmbMacroTag
{
  protected $method;

  function setRecursionMethod($name)
  {
    $this->method = $name;
  }

  protected function _generateContent($code)
  {
    if(!$level = $this->parent->get('level'))
      $level = '$level';

    if(!$as = $this->parent->get('as'))
      $as = '$item';

    if(!$kids_prop = $this->parent->get('kids_prop'))
      $kids_prop = 'kids';

    $before_item = $this->_getTagsBeforeItem();
    $after_item = $this->_getTagsAfterItem();
    $item = $this->findImmediateChildByClass('lmbMacroTreeItemTag');

    $code->writePHP('if(isset(' . $as . '["' . $kids_prop . '"])) {');

    foreach($before_item as $tag)
      $tag->generate($code);

    $item->generate($code);

    $code->writePHP('$this->' . $this->method . '(' . $as . '["' . $kids_prop . '"], ' . $level . ' + 1);');

    foreach($after_item as $tag)
      $tag->generate($code);

    $code->writePHP('} else {');

    parent :: _generateContent($code);

    $code->writePHP('}');
  }

  protected function _getTagsBeforeItem()
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

  protected function _getTagsAfterItem()
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
}

