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
 * The parent compile time component for lists
 * @tag list
 * @package macro
 * @version $Id$
 */
class lmbMacroListTag extends lmbMacroTag
{
  function generateContents($code)
  {
    $using = $this->get('using');
    $as = $this->get('as');

    $counter = $code->getTempVarRef();
    $code->writePHP($counter . ' = 0;');
    $code->writePHP('foreach(' . $using . ' as ' . $as . ') {');

    $glue = $this->findImmediateChildByClass('lmbMacroListGlueTag');

    $found_item_tag = false;
    $postponed_nodes = array();

    //we need to render all nodes before and after <%list:*%> tags
    foreach($this->children as $child)
    {
      //we want to skip all <%list:*%> tags, since they are rendered manually
      if(!$this->_isOneOfListTags($child))
      {
        //tags before <%list:item%> should be rendered only once when counter is 0
        if(!$found_item_tag)
        {
          $code->writePHP('if(' . $counter . ' == 0) {');
          $child->generateContents($code);
          $code->writePHP('}');
        }
        //otherwise we collect them to display later 
        else
          $postponed_nodes[] = $child;
      }
      elseif(is_a($child, 'lmbMacroListItemTag'))
      {
        $found_item_tag = true;
        if($glue)
        {
          $code->writePHP('if('. $counter . ' > 0) {');
          $glue->generateContents($code);
          $code->writePHP('}');
        }
        $child->generateContents($code);
      }
    }

    $code->writePHP($counter . '++;');
    $code->writePHP('}');

    //tags after <%list:item%> should be rendered only if there were any items
    foreach($postponed_nodes as $node)
    {
      $code->writePHP('if(' . $counter . ' > 0) {');
      $node->generateContents($code);
      $code->writePHP('}');
    }

    if($list_empty = $this->findImmediateChildByClass('lmbMacroListEmptyTag'))
    {
      $code->writePHP('if(' . $counter . ' == 0) {');
      $list_empty->generateContents($code);
      $code->writePHP('}');
    }
  }

  protected function _isOneOfListTags($node)
  {
    $classes = array('lmbMacroListEmptyTag',
                   'lmbMacroListItemTag',
                   'lmbMacroListGlueTag');

    foreach($classes as $class)
    {
      if(is_a($node, $class))
        return true;
    }
    return false;
  }
}

