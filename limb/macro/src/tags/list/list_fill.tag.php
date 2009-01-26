<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * Compile time component for output finalizers in a list
 * Allows to generate valid layout while output multicolumn lists
 * Default ratio attribute is 1  
 * @tag list:fill
 * @parent_tag_class lmbMacroListTag
 * @package macro
 * @version $Id$
 */
class lmbMacroListFillTag extends lmbMacroTag
{
  function preParse($compiler)
  {
    $list = $this->findParentByClass('lmbMacroListTag');
    $list->countSource();
    
    return parent :: preParse($compiler);
  }
  
  protected function _generateContent($code)
  {
    $ratio_var = $code->generateVar();
    if($ratio = $this->get('upto'))
      $code->writePHP($ratio_var . " = $ratio;\n");
    else
      $code->writePHP($ratio_var . " = 1;\n");

    $list = $this->findParentByClass('lmbMacroListTag');

    $count_var = $code->generateVar();
    $items_left_var = $code->generateVar();
    $code->writePhp($count_var .' = count('. $list->getSourceVar() . ');');
    
    $force = (int)$this->getBool('force');

    $code->writePhp("if (($force || ({$count_var}/{$ratio_var} > 1)) && {$count_var}) \n");
    $code->writePhp($items_left_var . " = ceil({$count_var}/{$ratio_var})*{$ratio_var} - {$count_var}; \n");
    $code->writePhp("else \n");
    $code->writePhp($items_left_var . " = 0;\n");

    $code->writePhp("if ({$items_left_var}){\n");

    if($items_left = $this->get('items_left'))
      $code->writePhp($items_left . " = {$items_left_var};");

    parent :: _generateContent($code);

    $code->writePhp('}'. "\n");
  }
}

