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
 * Compile time component for output finalizers in a list
 * Allows to generate valid layout while output multicolumn lists
 * Default ratio attribute is 1  * @tag list:fill
 * @package macro
 * @version $Id$
 */
class lmbMacroListFillTag extends lmbMacroTag
{
  function generateContents($code)
  {
    $ratio_var = $code->getTempVarRef();
    if($ratio = $this->get('upto'))
      $code->writePHP($ratio_var . " = $ratio;\n");
    else
      $code->writePHP($ratio_var . " = 1;\n");

    $list = $this->findParentByClass('lmbMacroListTag');

    $count_var = $code->getTempVarRef();
    $items_left_var = $code->getTempVarRef();
    $code->writePhp($count_var .' = count('. $list->getSourceVar() . ');');

    $code->writePhp("if ({$count_var}/{$ratio_var} > 1) \n");
    $code->writePhp($items_left_var . " = ceil({$count_var}/{$ratio_var})*{$ratio_var} - {$count_var}; \n");
    $code->writePhp("else \n");
    $code->writePhp($items_left_var . " = 0;\n");

    $code->writePhp("if ({$items_left_var}){\n");

    if($items_left = $this->get('items_left'))
      $code->writePhp($items_left . " = {$items_left_var};");

    parent :: generateContents($code);

    $code->writePhp('}'. "\n");
  }
}

