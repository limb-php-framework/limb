<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbTreeItemsNestingMakerTest.class.php 5008 2007-02-08 15:37:24Z pachanga $
 * @package    tree
 */
lmb_require('limb/datasource/src/lmbArrayDataset.class.php');
lmb_require('limb/datasource/src/lmbDatasetHelper.class.php');
lmb_require('limb/tree/src/dataset/lmbTreeItemsNestingMaker.class.php');

class lmbTreeItemsNestingMakerTest extends UnitTestCase
{
  function testMakeNestedOneElementRs()
  {
    $raw_tree_array = array(
      array('id' => 1, 'parent_id' => 0),
      );

    $expected_tree_array = array(
      array('id' => 1, 'parent_id' => 0),
      );

    $raw = new lmbArrayDataset($raw_tree_array);
    $nested = new lmbDatasetHelper(new lmbTreeItemsNestingMaker($raw));
    $arr = $nested->getArray();

    $this->assertEqual($arr, $expected_tree_array);
  }

  function testMakeNestedSimpleRs()
  {
    $raw_tree_array = array(
      array('id' => 1, 'parent_id' => 0),
        array('id' => 2, 'parent_id' => 1),
          array('id' => 5, 'parent_id' => 2),
        array('id' => 3, 'parent_id' => 1),
      array('id' => 4, 'parent_id' => 100),
      );

    $expected_tree_array = array(
      array('id' => 1, 'parent_id' => 0, 'children' =>
            array(
                  array('id' => 2, 'parent_id' => 1, 'children' => array(
                      array('id' => 5, 'parent_id' => 2),
                      )
                  ),
                  array('id' => 3, 'parent_id' => 1),
                  ),
            ),
      array('id' => 4, 'parent_id' => 100)
      );

    $raw = new lmbArrayDataset($raw_tree_array);
    $nested = new lmbDatasetHelper(new lmbTreeItemsNestingMaker($raw));
    $arr = $nested->getArray();

    $this->assertEqual($arr, $expected_tree_array);
  }

  function testMakeNestedMoreComplexRs()
  {
    $raw_tree_array = array(
      array('id' => 1, 'parent_id' => 0),
        array('id' => 2, 'parent_id' => 1),
          array('id' => 3, 'parent_id' => 2),
          array('id' => 4, 'parent_id' => 2),
        array('id' => 5, 'parent_id' => 1),
      array('id' => 6, 'parent_id' => 100),
        array('id' => 7, 'parent_id' => 6),
      array('id' => 8, 'parent_id' => 200),
    );

    $expected_tree_array = array(
      array('id' => 1,
            'parent_id' => 0,
            'children' =>  array(
               array('id' => 2,
                    'parent_id' => 1,
                    'children' => array(
                        array('id' => 3, 'parent_id' => 2),
                        array('id' => 4, 'parent_id' => 2),
                     )
               ),
               array('id' => 5,
                    'parent_id' => 1
               )
            )
      ),
      array('id' => 6,
            'parent_id' => 100,
            'children' => array(
                array('id' => 7, 'parent_id' => 6),
             )
      ),
      array('id' => 8, 'parent_id' => 200),
    );

    $raw = new lmbArrayDataset($raw_tree_array);
    $nested = new lmbDatasetHelper(new lmbTreeItemsNestingMaker($raw));
    $arr = $nested->getArray();

    $this->assertEqual($arr, $expected_tree_array);
  }
}

?>