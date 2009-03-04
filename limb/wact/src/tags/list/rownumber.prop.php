<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * @property ListRowNumber
 * @tag_class WactListItemTag
 * @package wact
 * @version $Id: rownumber.prop.php 7686 2009-03-04 19:57:12Z korchasa $
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


