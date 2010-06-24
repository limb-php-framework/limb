<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
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
    $this->assertEqual($errors[0]['message'], $message);
    $this->assertEqual($errors[0]['fields'], array('foo'));
    $this->assertEqual($errors[0]['values'], array('FOO'));
  }

  function testRenameFields()
  {
    $list = new lmbErrorList();

    $list->addError($message = '{Field_1} is field and {Field_2} also is a field',
      array('Field_1' => 'login', 'Field_2' => 'password'));
    $list->addError($message = "The greatest {Field_1}'s story is '{Field_2} and {Field_3}'",
      array('Field_1' => 'that man', 'Field_2' => 'that guy', 'Field_3' => 'that girl'));

    $new_field_names = array(
      'login' => 'custom_login',
      'password' => 'custom_password',
      'that man' => 'Shakespeare',
      'that guy' => 'Romeo',
      'that girl' => 'Juliet'
    );

    $list->renameFields($new_field_names);

    $errors = $list->export();

    $this->assertEqual($errors[0]['fields'], array('Field_1' => 'custom_login', 'Field_2' => 'custom_password'));
    $this->assertEqual($errors[1]['fields'], array('Field_1' => 'Shakespeare', 'Field_2' => 'Romeo', 'Field_3' => 'Juliet'));
  }

  function testRenameFieldsWithSimilarNames() {
    $list = new lmbErrorList();

    $list->addError($message = '{Field_1} must repeat {Field_2}',
      array('Field_1' => 'passretype', 'Field_2' => 'pass'));

    $new_field_names = array(
      'passretype' => 'secondary typed password',
      'pass' => 'first typed password'
    );

    $list->renameFields($new_field_names);

    $errors = $list->export();

    $this->assertEqual($errors[0]['fields'], array('Field_1' => $new_field_names['passretype'], 'Field_2' => $new_field_names['pass']));
  }


}
