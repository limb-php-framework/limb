<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbErrorListTest.class.php 5222 2007-03-13 13:26:27Z serega $
 * @package    validation
 */
lmb_require('limb/validation/src/lmbErrorList.class.php');

class lmbErrorListTest extends UnitTestCase
{
  function testAddFieldError()
  {
    $list = new lmbErrorList();

    $this->assertTrue($list->isValid());

    $list->addError($message = 'error_group', array('foo'), array('FOO'));

    $this->assertFalse($list->isValid());

    $errors = $list->export();
    $this->assertEqual(sizeof($errors), 1);
    $this->assertEqual($errors[0]->get('message'), $message);
    $this->assertEqual($errors[0]->getFieldsList(), array('foo'));
    $this->assertEqual($errors[0]->getValuesList(), array('FOO'));
  }
}
?>