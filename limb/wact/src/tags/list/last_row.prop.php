<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * @property LastRow
 * @tag_class WactListItemTag
 * @package wact
 * @version $Id$
 */
class WactListRowLastRowProperty extends WactCompilerProperty
{
  protected $temp_var;
  protected $count_var;
  protected $has_increment = FALSE;

  function generateScopeEntry($code)
  {
    $this->temp_var = $code->getTempVarRef();
    $this->count_var = $code->getTempVarRef();

    $ListList = $this->context->findParentByClass('WactListListTag');

    $code->writePHP($this->temp_var . " = 0; \n");
    $code->writePHP($this->count_var . ' = ' . $ListList->getComponentRefCode() . '->countPaginated();' . "\n");
  }

  function generatePreStatement($code_writer)
  {
    if (!$this->has_increment)
    {
      $this->hasIncrement = TRUE;
      $code_writer->writePHP($this->temp_var . '++;');
    }
  }

  function generateExpression($code)
  {
    if(!$this->has_increment)
      $this->has_increment = TRUE;

    $ListList = $this->context->findParentByClass('WactListListTag');
    $code->writePHP('(('.$this->temp_var .' == '. $this->count_var . ')'.'?1:0)');
  }
}
