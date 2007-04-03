<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: rownumber.prop.php 5021 2007-02-12 13:04:07Z pachanga $
 * @package    wact
 */

/**
 * @property ListRowNumber
 * @tag_class WactListItemTag
 */
class WactListRowNumberProperty extends WactCompilerProperty
{
  protected $temp_var;
  protected $has_increment = FALSE;

  function generateScopeEntry($code)
  {
    $this->temp_var = $code->getTempVarRef();

    $ListList = $this->context->findParentByClass('WactListListTag');

    $code->writePHP($this->temp_var . ' = ' . $ListList->getComponentRefCode() . '->getOffset();' . "\n");
  }

  function generateExpression($code)
  {
    if(!$this->has_increment)
    {
      $code->writePHP('++');
      $this->has_increment = TRUE;
    }
    $code->writePHP($this->temp_var);
  }

}

?>