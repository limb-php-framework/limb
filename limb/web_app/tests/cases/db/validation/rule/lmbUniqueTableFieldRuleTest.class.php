<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/validation/tests/cases/rule/lmbValidationRuleTestCase.class.php');
lmb_require('limb/dbal/src/lmbSimpleDb.class.php');
lmb_require('limb/web_app/src/validation/rule/lmbUniqueTableFieldRule.class.php');

class lmbUniqueTableFieldRuleTest extends lmbValidationRuleTestCase
{
  var $db = null;

  function setUp()
  {
    parent :: setUp();

    $toolkit = lmbToolkit :: instance();
    $conn = $toolkit->getDefaultDbConnection();
    $this->db = new lmbSimpleDb($conn);
    $this->db->insert('test_table', array('field1' => 1, 'field2' => 'wow'), null);
    $this->db->insert('test_table', array('field1' => 2, 'field2' => 'blah'), null);
  }

  function tearDown()
  {
    parent :: tearDown();
  }

  function testFieldValid()
  {
    $rule = new lmbUniqueTableFieldRule('test', 'test_table', 'field1');

    $data = new lmbSet();
    $data->set('test', -10000);

    $this->error_list->expectNever('addError');

    $rule->validate($data, $this->error_list);
  }

  function testFieldNotValid()
  {
    $rule = new lmbUniqueTableFieldRule('test', 'test_table', 'field2');

    $data = new lmbSet();
    $data->set('test', 'wow');

    $this->error_list->expectOnce('addError',
                                  array(lmb_i18n('{Field} must have other value since {Value} already exists', 'web_app'),
                                        array('Field' => 'test'),
                                        array('Value' => 'wow')));


    $rule->validate($data, $this->error_list);

  }
  function testFieldNotValid2()
  {
    $rule = new lmbUniqueTableFieldRule('test', 'test_table', 'field1');

    $data = new lmbSet();
    $data->set('test', "001");

    $this->error_list->expectOnce('addError',
                                  array(lmb_i18n('{Field} must have other value since {Value} already exists', 'web_app'),
                                        array('Field' => 'test'),
                                        array('Value' => '001')));


    $rule->validate($data, $this->error_list);
  }

  function testFieldNotValidSelfError()
  {
    $rule = new lmbUniqueTableFieldRule('test', 'test_table', 'field2', $message = "ERROR_DUPLICATE_WOW");

    $data = new lmbSet();
    $data->set('test', 'wow');

    $this->error_list->expectOnce('addError',
                                  array($message, array('Field' => 'test'), array('Value' => 'wow')));

    $rule->validate($data, $this->error_list);
  }
}


