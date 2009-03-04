<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * Compile time component for items (rows) in the list
 * @tag list:ITEM
 * @parent_tag_class WactListListTag
 * @package wact
 * @version $Id: item.tag.php 7686 2009-03-04 19:57:12Z korchasa $
 */
class WactListItemTag extends WactRuntimeDatasourceComponentTag
{
  protected $runtimeComponentName = 'WactDatasourceRuntimeComponent';

  function generateBeforeContent($code_writer)
  {
    $separators = $this->findImmediateChildrenByClass('WactListSeparatorTag');
    foreach($separators as $separator)
    {
      $code_writer->writePhp($separator->getComponentRefCode($code_writer) . '->prepare();' . "\n");
    }

    $list = $this->findParentByClass('WactListListTag');

    $code_writer->writePHP('do { ' . "\n");
    $code_writer->writePHP($this->getComponentRefCode() . '->registerDataSource(' .
                    $list->getComponentRefCode() . '->current());' . "\n");

    parent :: generateBeforeContent($code_writer);
  }

  function generateAfterContent($code_writer)
  {
    parent :: generateAfterContent($code_writer);

    $list = $this->findParentByClass('WactListListTag');

    $code_writer->writePHP($list->getComponentRefCode() . '->next();' . "\n");
    $code_writer->writePHP('} while (' . $list->getComponentRefCode() . '->valid());' . "\n");
  }
}

