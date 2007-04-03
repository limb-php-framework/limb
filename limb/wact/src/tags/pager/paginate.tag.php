<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id$
 * @package    wact
 */

/**
 * Links a list with a pager
 * @tag paginate
 * @forbid_end_tag
 * @req_const_attributes list with
 */
class WactPaginateTag extends WactCompilerTag
{
  function generateContents($code)
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

?>