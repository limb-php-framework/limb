<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbSetterDataSetDecoratorTest.class.php 4992 2007-02-08 15:35:40Z pachanga $
 * @package    datasource
 */
lmb_require('limb/datasource/src/lmbSetterDataSetDecorator.class.php');
lmb_require('lmbPagedArrayDataSet.class.php');

class lmbSetterDataSetDecoratorTest extends UnitTestCase
{
  function testTag()
  {
    $raw = new lmbPagedArrayDataSet(array(array()));
    $decorator = new lmbSetterDataSetDecorator($raw);
    $decorator->setGroupName('spec');
    $decorator->setNodePath('anything');
    $decorator->rewind();
    $record = $decorator->current();
    $this->assertEqual($record->get('group_name'), 'spec');
    $this->assertEqual($record->get('node_path'), 'anything');
  }
}
?>
