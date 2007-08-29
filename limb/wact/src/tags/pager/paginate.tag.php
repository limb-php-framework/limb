<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * Links a list with a pager
 * @tag paginate
 * @forbid_end_tag
 * @req_const_attributes list with
 * @package wact
 * @version $Id$
 */
class WactPaginateTag extends WactCompilerTag
{
  function generateTagContent($code)
  {
    if(!$list = $this->parent->findUpChild($this->getAttribute('list')))
      $this->raiseCompilerError('List tag is not found', array('id' => $this->getAttribute('list')));

    if(!$pager = $this->parent->findUpChild($this->getAttribute('with')))
      $this->raiseCompilerError('Pager navigator tag is not found', array('id' => $this->getAttribute('with')));

   $dataset_var = $code->getTempVarRef();

   $code->writePhp($dataset_var . ' = ' . $list->getComponentRefCode()  . '->getDataset();' . "\n");
   $code->writePhp($pager->getComponentRefCode() . '->setPagedDataSet(' . $dataset_var  . ');' . "\n");
   $code->writePhp($dataset_var . '->paginate(' . $pager->getComponentRefCode()  . '->getStartingItem(), ' .
                                                $pager->getComponentRefCode()  . '->getItemsPerPage());' . "\n");
  }
}


