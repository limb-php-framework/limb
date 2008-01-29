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

    $code->writePHP('foreach(' . $this->source_var . ' as ' . $as . ') {');

    if($user_counter = $this->get('counter'))
      $code->writePHP($user_counter . ' = ' . $this->counter_var . '+1;');

    if($parity = $this->get('parity'))
      $code->writePHP($parity . ' = (( (' . $this->counter_var . ' + 1) % 2) ? "odd" : "even");');

    $found_item_tag = false;
    $postponed_nodes = array();

    foreach($this->children as $child)
    {
      //we want to skip some of  {{list:*}} tags, since they are rendered manually
      if(!$this->_isOneOfListTags($child))
      {
        //tags before {{list:item}} should be rendered only once when counter is 0
        if(!$found_item_tag)
        {
          $code->writePHP('if(' . $this->counter_var . ' == 0) {');
          $child->generate($code);
          $code->writePHP('}');
        }
        //otherwise we collect them to display later
        else
          $postponed_nodes[] = $child;
      }
      elseif(is_a($child, 'lmbMacroListItemTag'))
      {
        $found_item_tag = true;
        $child->generate($code);
      }
    }

    $code->writePHP($this->counter_var . '++;');
    $code->writePHP('}');

    //tags after {{list:item}} should be rendered only if there were any items
    foreach($postponed_nodes as $node)
    {
      $code->writePHP('if(' . $this->counter_var . ' > 0) {');
      $node->generate($code);
      $code->writePHP('}');
    }

    $this->_renderEmptyTag($code);
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
    $item_var = $code->generateVar();

    $code->writePHP("\nif(!is_array({$using}) && !({$using} instanceof Iterator)) {\n");
      $code->writePHP("{$using}  = array();}\n");
    
    if($this->count_source)
    {
      $code->writePHP($this->source_var . " = array();\n");
      $code->writePHP('foreach(' . $using . " as $item_var) {\n");
        $code->writePHP($this->source_var . "[] = $item_var;\n");
      $code->writePHP("}\n");
    }
    else
      $code->writePHP($this->source_var . " = {$using};\n");
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

