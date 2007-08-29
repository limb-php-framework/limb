<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * @property ListRowNumber
 * @tag_class WactListItemTag
 * @package wact
 * @version $Id: rownumber.prop.php 6243 2007-08-29 11:53:10Z pachanga $
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


