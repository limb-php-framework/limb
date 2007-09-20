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
    $counter = $code->getTempVarRef();
    $using = $this->get('using');
    $as = $this->get('as');

    $list_item = $this->findImmediateChildByClass('lmbMacroListItemTag');
    $list_empty = $this->findImmediateChildByClass('lmbMacroListEmptyTag');

    $code->writePHP($counter . ' = 0;');
    $code->writePHP('foreach(' . $using . ' as ' . $as . ') {');
    $list_item->generateContents($code);
    $code->writePHP($counter . '++;');
    $code->writePHP('}');

    if($list_empty)
    {
      $code->writePHP('if(' . $counter . ' == 0) {');
      $list_empty->generateContents($code);
      $code->writePHP('}');
    }
  }
}

