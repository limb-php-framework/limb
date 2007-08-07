<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
require_once('limb/active_record/src/lmbActiveRecord.class.php');
require_once('limb/dbal/src/lmbSimpleDb.class.php');

class PersonForTest extends lmbActiveRecord
{
  public $save_count = 0;
  protected $_has_one = array('social_security' => array('field' => 'ss_id',
                                                         'class' => 'SocialSecurityForTest',
                                                         'can_be_null' => true));

  function _onSave()
  {
    $this->save_count++;
  }

}

class PersonForTestNoCascadeDelete extends lmbActiveRecord
{
  protected $_db_table_name = 'person_for_test';
  protected $_has_one = array('social_security' => array('field' => 'ss_id',
                                                         'class' => 'SocialSecurityForTest',
                                                         'can_be_null' => true,
                                                         'cascade_delete' => false));
}

class PersonForTestWithRequiredSocialSecurity extends lmbActiveRecord
{
  protected $_db_table_name = 'person_for_test';
  protected $_has_one = array('social_security' => array('field' => 'ss_id',
                                                         'class' => 'SocialSecurityForTest',
                                                         'can_be_null' => true));

  function _createValidator()
  {
    $validator = new lmbValidator();
    $validator->addRequiredObjectRule('social_security');
    return $validator;
  }
}

class SocialSecurityForTest extends lmbActiveRecord
{
  protected $_belongs_to = array('person' => array('field' => 'ss_id',
                                                   'class' => 'PersonForTest'));
}

class lmbAROneToOneRelationsTest extends UnitTestCase
{
  protected $db;

  function setUp()
  {
    $this->db = new lmbSimpleDb(lmbToolkit :: instance()->getDefaultDbConnection());
    $this->_dbCleanUp();
  }

  function tearDown()
  {
    $this->_dbCleanUp();
  }

  function _dbCleanUp()
  {
    $this->db->delete('person_for_test');
    $this->db->delete('social_security_for_test');
  }

  function testNewObjectReturnsNullChild()
  {
    $person = new PersonForTest();
    $this->assertNull($person->getSocialSecurity());
  }

  function testNewObjectReturnsNullParent()
  {
    $number = new SocialSecurityForTest();
    $this->assertNull($number->getPerson());
  }

  function testSaveChild()
  {
    $person = new PersonForTest();
    $person->setName('Jim');
    $number = new SocialSecurityForTest();
    $number->setCode('099123');

    $person->setSocialSecurity($number);

    $this->assertNull($number->getId());

    $person->save();

    $this->assertNotNull($number->getId());
  }

  function testSaveParent()
  {
    $person = new PersonForTest();
    $person->setName('Jim');

    $number = new SocialSecurityForTest();
    $number->setCode('099123');

    $number->setPerson($person);

    $this->assertNull($person->getId());

    $number->save();

    $this->assertNotNull($person->getId());
  }

  function testDontSaveParentSecondTimeIfChildWasChanged()
  {
    $person = new PersonForTest();
    $person->setName('Jim');

    $number = new SocialSecurityForTest();
    $number->setCode('099123');

    $person->setSocialSecurity($number);
    $person->save();

    $this->assertEqual($person->save_count, 1);

    $person->save();

    $this->assertEqual($person->save_count, 1);
  }

  function testSavingParentSavesChildAsWell()
  {
    $person = new PersonForTest();
    $person->setName('Jim');

    $number = new SocialSecurityForTest();
    $number->setCode('099123');

    $person->setSocialSecurity($number);
    $person->save();

    $number->setCode($new_code = '0022112');
    $person->save();

    $loaded_number = new SocialSecurityForTest($number->getId());
    $this->assertEqual($loaded_number->getCode(), $new_code);
  }

  function testChangingChildObjectIdDirectly()
  {
    $person = new PersonForTest();
    $person->setName('Jim');

    $number1 = new SocialSecurityForTest();
    $number1->setCode('099123');

    $person->setSocialSecurity($number1);
    $person->save();

    $number2 = new SocialSecurityForTest();
    $number2->setCode('143453');
    $number2->save();

    $person2 = new PersonForTest($person->getId());
    $this->assertEqual($person2->getSocialSecurity()->getId(), $number1->getId());

    $person2->set('ss_id', $number2->getId());
    $person2->save();

    $person3 = new PersonForTest($person->getId());
    $this->assertEqual($person3->getSocialSecurity()->getId(), $number2->getId());
  }

  function testChangingChildIdRelationFieldDirectlyHasNoAffectIfChildObjectPropertyIsDirty()
  {
    $person = new PersonForTest();
    $person->setName('Jim');

    $number1 = new SocialSecurityForTest();
    $number1->setCode('099123');

    $person->setSocialSecurity($number1);
    $person->save();

    $number2 = new SocialSecurityForTest();
    $number2->setCode('143453');
    $number2->save();

    $person2 = new PersonForTest($person->getId());
    $this->assertEqual($person2->getSocialSecurity()->getId(), $number1->getId());

    $person2->set('ss_id', $number2->getId()); // changing child relation field directly
    $person2->setSocialSecurity($number1); // and making child object dirty
    $person2->save();

    $person3 = new PersonForTest($person->getId());
    $this->assertEqual($person3->getSocialSecurity()->getId(), $number1->getId());
  }

  function testLoadParentObject()
  {
    $person = new PersonForTest();
    $person->setName('Jim');
    $number = new SocialSecurityForTest();
    $number->setCode('099123');

    $person->setSocialSecurity($number);

    $person->save();

    $number2 = lmbActiveRecord :: findById('SocialSecurityForTest', $number->getId());

    $person2 = $number2->getPerson();

    $this->assertEqual($person2->getId(), $person->getId());
    $this->assertEqual($person2->getName(), 'Jim');
  }

  function testGenericGetLoadsChildObject()
  {
    $person = new PersonForTest();
    $person->setName('Jim');
    $number = new SocialSecurityForTest();
    $number->setCode('099123');

    $person->setSocialSecurity($number);

    $person->save();

    $number2 = lmbActiveRecord :: findById('SocialSecurityForTest', $number->getId());

    $person2 = $number2->getPerson();

    $this->assertEqual($person2->getId(), $person->getId());
    $this->assertEqual($person2->getName(), 'Jim');
  }

  function testLoadChildObject()
  {
    $person = new PersonForTest();
    $person->setName('Jim');
    $number = new SocialSecurityForTest();
    $number->setCode('099123');

    $person->setSocialSecurity($number);

    $person_id = $person->save();

    $person2 = lmbActiveRecord :: findById('PersonForTest', $person_id);
    $number2 = $person2->getSocialSecurity();

    $this->assertEqual($person2->getId(), $person_id);
    $this->assertEqual($person2->getName(), 'Jim');
    $this->assertEqual($number2->getId(), $number->getId());
    $this->assertEqual($number2->getCode(), '099123');
  }

  function testGenericGetLoadsParentObject()
  {
    $person = new PersonForTest();
    $person->setName('Jim');
    $number = new SocialSecurityForTest();
    $number->setCode('099123');

    $person->setSocialSecurity($number);

    $person_id = $person->save();

    $person2 = lmbActiveRecord :: findById('PersonForTest', $person_id);
    $number2 = $person2->get('social_security');

    $this->assertEqual($person2->getId(), $person_id);
    $this->assertEqual($person2->getName(), 'Jim');
    $this->assertEqual($number2->getId(), $number->getId());
    $this->assertEqual($number2->getCode(), '099123');
  }

  function testParentRemovalDeletesChildren()
  {
    $person = new PersonForTest();
    $person->setName('Jim');
    $number = new SocialSecurityForTest();
    $number->setCode('099123');

    $person->setSocialSecurity($number);

    $person_id = $person->save();
    $this->assertTrue($number_id = $number->getId());

    $person->destroy();

    $this->assertNull(lmbActiveRecord :: findFirst('SocialSecurityForTest', array('criteria' => 'id = ' . $number_id)));
    $this->assertNull(lmbActiveRecord :: findFirst('PersonForTest', array('criteria' => 'id = ' . $person_id)));
  }

  function testParentDeleteAllDeletesChildren()
  {
    $person = new PersonForTest();
    $person->setName('Jim');
    $number = new SocialSecurityForTest();
    $number->setCode('099123');

    $person->setSocialSecurity($number);

    $person_id = $person->save();
    $number_id = $number->getId();

    //this one should stay
    $untouched_number = new SocialSecurityForTest();
    $untouched_number->setCode('0893127');
    $untouched_number->save();

    lmbActiveRecord :: delete('PersonForTest');

    $this->assertNull(lmbActiveRecord :: findFirst('SocialSecurityForTest', array('criteria' => 'id = ' . $number_id)));
    $this->assertNull(lmbActiveRecord :: findFirst('PersonForTest', array('criteria' => 'id = ' . $person_id)));

    $number2 = lmbActiveRecord :: findById('SocialSecurityForTest', $untouched_number->getId());
    $this->assertEqual($number2->getCode(), '0893127');
  }

  function testParentRemovalWithNoCascadeDeleteChildren()
  {
    $person = new PersonForTestNoCascadeDelete();
    $person->setName('Jim');
    $number = new SocialSecurityForTest();
    $number->setCode('099123');

    $person->setSocialSecurity($number);

    $person_id = $person->save();
    $this->assertTrue($number_id = $number->getId());

    $person->destroy();

    $ss2 = lmbActiveRecord :: findFirst('SocialSecurityForTest', array('criteria' => 'id = ' . $number_id));
    $this->assertEqual($ss2->getCode(), $number->getCode());
  }

  function testChildRemovalNullifyParentField()
  {
    $number = new SocialSecurityForTest();
    $number->setCode('099123');

    $person = new PersonForTest();
    $person->setName('Jim');

    $person->setSocialSecurity($number);
    $number->setPerson($person);
    $person->setNumber($number);
    $person->save();

    $number->destroy();

    $person2 = new PersonForTest($person->getId());
    $this->assertNull($person2->get('ss_id'));
  }

  function testChildRemovalWithRequiredObjectInParentRelationDefinitionThrowsValidationException()
  {
    $number = new SocialSecurityForTest();
    $number->setCode('099123');

    $person = new PersonForTestWithRequiredSocialSecurity();
    $person->setName('Jim');

    $person->setSocialSecurity($number);
    $number->setPerson($person);
    $person->save();

    try
    {
      $number->destroy();
      $this->assertTrue(false);
    }
    catch(lmbValidationException $e)
    {
      $this->assertTrue(true);
    }

    $number2 = lmbActiveRecord :: findFirst('SocialSecurityForTest');
    $this->assertNotNull($number2, 'Removal should not be finished');
    $this->assertEqual($number2->getId(), $number->getId());
  }

  function testSettingNullDetachesChildObject()
  {
    $person = new PersonForTest();
    $person->setName('Jim');
    $number = new SocialSecurityForTest();
    $number->setCode('099123');

    $person->setSocialSecurity($number);
    $person->save();

    $person->setSocialSecurity(null);
    $person_id = $person->save();

    $person2 = new PersonForTest($person_id);
    $this->assertNull($person2->getSocialSecurity());

    $number2 = new SocialSecurityForTest($number->getId());
    $this->assertEqual($number2->getCode(), $number->getCode());
  }

  function testDontResetParentIfChildImport()
  {
    $person = new PersonForTest();
    $person->setName('Jim');
    $number = new SocialSecurityForTest();
    $number->setCode('099123');
    $person->setSocialSecurity($number);
    $person->save();

    $source = array('name' => $person->getName());

    $person2 = new PersonForTest($person->getid());
    $person2->save();

    $this->assertEqual($person2->getName(), $person->getName());
    $this->assertEqual($person2->getSocialSecurity()->getCode(), $number->getCode());
  }
}


