<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
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

    $before_node = $this->_getTagsBeforeNode();
    $after_node = $this->_getTagsAfterNode();
    $tree_node = $this->findImmediateChildByClass('lmbMacroTreeNodeTag');

    $tree = $this->get('using');

    $items = $code->generateVar();
    $counter = $code->generateVar();
    $extra_params = $code->generateVar();

    $this->method = $code->beginMethod('_render_tree'. self::generateUniqueId(), array($items, $level, $extra_params . '= array()'));

    $code->writePHP("if($extra_params) extract($extra_params);");

    $code->writePHP($counter . "=0;\n");

    $code->writePHP('foreach(' . $items . ' as ' . $as . ") {\n");

    if($user_counter = $this->get('counter'))
      $code->writePHP($user_counter . ' = ' . $counter . "+1;\n");

    //rendering tags before branch
    $code->writePHP('if(!' . $counter . ") {\n");
    foreach($before_node as $tag)
      $tag->generate($code);
    $code->writePHP("}\n");

    $tree_node->generate($code);

    $code->writePHP($counter . "++;\n");
    $code->writePHP("}\n");//foreach

    $code = $this->_renderEmptyTag($code, $items);

    //rendering tags after branch
    $code->writePHP('if(' . $counter . ") {\n");
    foreach($after_node as $tag)
      if(!$tag instanceof lmbMacroTreeEmptyTag)
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

  protected function _getTagsBeforeNode()
  {
    $tags = array();
    foreach($this->children as $child)
    {
      if($child instanceof lmbMacroTreeNodeTag)
        break;
      $tags[] = $child;
    }
    return $tags;
  }

  protected function _getTagsAfterNode()
  {
    $tags = array();
    $collect = false;
    foreach($this->children as $child)
    {
      if($collect)
        $tags[] = $child;
      if($child instanceof lmbMacroTreeNodeTag)
        $collect = true;
    }
    return $tags;
  }

  function extraAttributesIntoArrayString()
  {
    return $this->attributesIntoArrayString($skip = array('as', 'using', 'counter', 'level'));
  }

  protected function _renderEmptyTag($code, $items)
  {
    if($list_empty = $this->findImmediateChildByClass('lmbMacroTreeEmptyTag'))
    {
      $code->writePHP('if(count(' . $items . ') == 0) {');
        $list_empty->generate($code);
      $code->writePHP('}');
    }

    return $code;
  }
}
