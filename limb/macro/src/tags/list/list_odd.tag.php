<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * Renders a portion of the template if the current list row is odd
 * @tag list:odd
 * @parent_tag_class lmbMacroListItemTag
 * @package macro
 * @version $Id$
 */
class lmbMacroListRowOddTag extends lmbMacroTag
{
  protected function _generateContent($code)
  {
    $list = $this->findParentByClass('lmbMacroListTag');
    $counter_var = $list->getCounterVar();

    $code->writePHP('if(('. $counter_var . ' + 1) % 2 != 0) {');
    parent :: _generateContent($code);
    $code->writePHP('}');
  }
}

