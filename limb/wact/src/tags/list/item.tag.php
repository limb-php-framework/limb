<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: item.tag.php 5933 2007-06-04 13:06:23Z pachanga $
 * @package    $package$
 */

/**
 * Compile time component for items (rows) in the list
 * @tag list:ITEM
 * @parent_tag_class WactListListTag
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
?>