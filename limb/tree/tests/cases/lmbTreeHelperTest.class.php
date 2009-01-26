<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/tree/src/lmbTreeHelper.class.php');
lmb_require('limb/core/src/lmbCollection.class.php');

class lmbTreeHelperTest extends UnitTestCase
{
  function testSortComplexRs()
  {
    $raw_tree_array = array(
      array('id' => 1, 'parent_id' => 0, 'sort1' => 'bill', 'sort2' => 0),
        array('id' => 2, 'parent_id' => 1, 'sort1' => 'body', 'sort2' => 1),
          array('id' => 3, 'parent_id' => 2, 'sort1' => 'merfy', 'sort2' => 0),
          array('id' => 4, 'parent_id' => 2, 'sort1' => 'eddy', 'sort2' => 1),
        array('id' => 5, 'parent_id' => 1, 'sort1' => 'body', 'sort2' => 0),
      array('id' => 6, 'parent_id' => 0, 'sort1' => 'alfred', 'sort2' => 1),
        array('id' => 7, 'parent_id' => 6, 'sort1' => 'tom', 'sort2' => 0),
      array('id' => 8, 'parent_id' => 0, 'sort1' => 'cunny', 'sort2' => 4),
    );

    $expected_tree_array = array(
      array('id' => 8, 'parent_id' => 0, 'sort1' => 'cunny', 'sort2' => 4),
      array('id' => 1, 'parent_id' => 0, 'sort1' => 'bill', 'sort2' => 0),
        array('id' => 5, 'parent_id' => 1, 'sort1' => 'body', 'sort2' => 0),
        array('id' => 2, 'parent_id' => 1, 'sort1' => 'body', 'sort2' => 1),
          array('id' => 3, 'parent_id' => 2, 'sort1' => 'merfy', 'sort2' => 0),
          array('id' => 4, 'parent_id' => 2, 'sort1' => 'eddy', 'sort2' => 1),
      array('id' => 6, 'parent_id' => 0, 'sort1' => 'alfred', 'sort2' => 1),
        array('id' => 7, 'parent_id' => 6, 'sort1' => 'tom', 'sort2' => 0),
    );

    $sorted = lmbTreeHelper :: sort(new lmbCollection($raw_tree_array),
                                      array('sort1' => 'DESC', 'sort2' => 'ASC'));

    $to_check = array();
    foreach($sorted as $record)
      $to_check[] = $record->export();

    $this->assertEqual($to_check, $expected_tree_array);
  }
}


