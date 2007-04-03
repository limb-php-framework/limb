<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbEmptyIteratorTest.class.php 4992 2007-02-08 15:35:40Z pachanga $
 * @package    datasource
 */
lmb_require('limb/datasource/src/lmbEmptyIterator.class.php');

class lmbEmptyIteratorTest extends UnitTestCase
{
  function testIsNotValid()
  {
    $iterator = new lmbEmptyIterator();
    $this->assertFalse($iterator->valid());
    $iterator->rewind();
    $this->assertFalse($iterator->valid());
  }
}
?>