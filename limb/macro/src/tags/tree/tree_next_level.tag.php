<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * @tag tree:nextlevel
 * @aliases tree:item
 * @package macro
 * @version $Id$
 */
class lmbMacroTreeNextLevelTag extends lmbMacroTag
{
  protected function _generateContent($code)
  {
    parent :: _generateContent($code);
    
    $tree_tag = $this->findParentByClass('lmbMacroTreeTag');
    
    if(!$level = $tree_tag->get('level'))
      $level = '$level';

    if(!$as = $tree_tag->get('as'))
      $as = '$item';

    if(!$kids_prop = $tree_tag->get('kids_prop'))
      $kids_prop = 'kids';
    
    $arg_str = $this->attributesIntoArrayString();

    $code->writePHP('if(isset(' . $as . '["' . $kids_prop . '"])) {');
    
    $method = $tree_tag->getRecursionMethod();
    
    $code->writePHP('$this->' . $method . '(' . $as . '["' . $kids_prop . '"], ' . $level . ' + 1, ' . $arg_str . ");\n");

    $code->writePHP('}');
  }  
}

