<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * The parent compile time component for lists
 * @tag list
 * @aliases list:list 
 * @req_attributes using
 * @package macro
 * @version $Id$
 */
class lmbMacroListTag extends lmbMacroTag
{
  protected $counter_var_var;
  protected $count_source = false;
  
  function preParse($compiler)
  {
    if(!$this->has('using') && $this->has('for'))
      $this->set('using', $this->get('for'));
    
    return parent :: preParse($compiler);
  }

  function countSource()
  {
    $this->count_source = true;
  }

  protected function _generateContent($code)
  {
    if(!$as = $this->get('as'))
      $as = '$item';

    //internal list counter
    $this->counter_var = $code->generateVar();
    $code->writePHP($this->counter_var . ' = 0;');

    $this->_prepareSourceVar($code);

    $this->_initializeGlueTags($code);
    
    $code->writePHP('foreach(' . $this->source_var . ' as ' . $as . ') {');

    if($user_counter = $this->get('counter'))
      $code->writePHP($user_counter . ' = ' . $this->counter_var . '+1;');

    if($parity = $this->get('parity'))
      $code->writePHP($parity . ' = (( (' . $this->counter_var . ' + 1) % 2) ? "odd" : "even");');

    $found_item_tag = false;
    $postponed_nodes = array();

    //tags before {{list:item}} should be rendered only once when counter is 0
    $code->writePHP('if(' . $this->counter_var . ' == 0) {');
    foreach($this->children as $child)
    {
      //we want to skip some of  {{list:*}} tags, since they are rendered manually
      if(!$this->_isOneOfListTags($child))
      {
        if(!$found_item_tag)
        {
          $child->generate($code);
        }
        //collectng postponed nodes to display later
        else
          $postponed_nodes[] = $child;
      }
      elseif(is_a($child, 'lmbMacroListItemTag'))
      {
        $found_item_tag = true;
        $code->writePHP('}');
        $child->generate($code);
      }
    }

    $code->writePHP($this->counter_var . '++;');
    $code->writePHP('}');

    //tags after {{list:item}} should be rendered only if there were any items
    $code->writePHP('if(' . $this->counter_var . ' > 0) {');
    foreach($postponed_nodes as $node)
      $node->generate($code);
    $code->writePHP('}');

    $this->_renderEmptyTag($code);
  }
  
  protected function _initializeGlueTags($code)
  {
    if(!$list_item_tag = $this->findChildByClass('lmbMacroListItemTag'))
      $this->raise('{{list:item}} tag is not found for {{list}} tag but required');
    
    $glue_tags = $list_item_tag->findImmediateChildrenByClass('lmbMacroListGlueTag');
    foreach($glue_tags as $glue_tag)
      $glue_tag->generateInitCode($code);
  }

  function getCounterVar()
  {
    return $this->counter_var;
  }

  function getSourceVar()
  {
    return $this->source_var;
  }

  protected function _isOneOfListTags($node)
  {
    $classes = array('lmbMacroListEmptyTag',
                     'lmbMacroListItemTag');

    foreach($classes as $class)
    {
      if(is_a($node, $class))
        return true;
    }
    return false;
  }

  protected function _prepareSourceVar($code)
  {
    $using = $this->get('using');

    $this->source_var = $code->generateVar();
    $temp_using = $code->generateVar();
    $item_var = $code->generateVar();

    $code->writePHP("{$temp_using} = {$using};\n");
    $code->writePHP("\nif(!is_array({$temp_using}) && !({$temp_using} instanceof Iterator)) {\n");
      $code->writePHP("{$temp_using} = array();}\n");
    
    if($this->count_source)
    {
      $code->writePHP($this->source_var . " = array();\n");
      $code->writePHP('foreach(' . $temp_using . " as $item_var) {\n");
        $code->writePHP($this->source_var . "[] = $item_var;\n");
      $code->writePHP("}\n");
    }
    else
      $code->writePHP($this->source_var . " = {$temp_using};\n");
  }

  protected function _renderEmptyTag($code)
  {
    if($list_empty = $this->findImmediateChildByClass('lmbMacroListEmptyTag'))
    {
      $code->writePHP('if(' . $this->counter_var . ' == 0) {');
      $list_empty->generate($code);
      $code->writePHP('}');
    }
  }
}

