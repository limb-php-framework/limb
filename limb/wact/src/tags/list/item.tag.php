<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: item.tag.php 5174 2007-03-02 09:53:27Z serega $
 * @package    wact
 */

/**
 * Compile time component for items (rows) in the list
 * @tag list:ITEM
 * @parent_tag_class WactListListTag
 */
class WactListItemTag extends WactRuntimeDatasourceComponentTag
{
  protected $runtimeComponentName = 'WactDatasourceRuntimeComponent';

  function generateContents($code_writer)
  {
    $list = $this->findParentByClass('WactListListTag');

    $code_writer->writePHP('do { ' . "\n");
    $code_writer->writePHP($this->getComponentRefCode() . '->registerDataSource(' .
                    $list->getComponentRefCode() . '->current());' . "\n");

    parent::generateContents($code_writer);

    $code_writer->writePHP($list->getComponentRefCode() . '->next();' . "\n");
    $code_writer->writePHP('} while (' . $list->getComponentRefCode() . '->valid());' . "\n");
  }
}
?>