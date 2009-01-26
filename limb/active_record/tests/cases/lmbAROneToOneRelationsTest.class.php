<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

class PersonForTestNoCascadeDelete extends lmbActiveRecord
{
  protected $_db_table_name = 'person_for_test';
  protected $_has_one = array('social_security' => array('field' => 'ss_id',
                                                         'class' => 'SocialSecurityForTest',
                                                         'can_be_null' => true,
                                                         'cascade_delete' => false));
}

class PersonForTestWithNotRequiredSocialSecurity extends lmbActiveRecord
{
  protected $_db_table_name = 'person_for_test';
  protected $_has_one = array('social_security' => array('field' => 'ss_id',
                                                         'class' => 'SocialSecurityForTest',
                                                         'can_be_null' => true,
                                                         'throw_exception_on_not_found' => false));
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

class lmbAROneToOneRelationsTest extends lmbARBaseTestCase
{
  protected $tables_to_cleanup = array('person_for_test', 'social_security_for_test'); 
  
  function testHas()
  {
    $person = new PersonForTest();
    $this->assertTrue(isset($person['social_security']));
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
    $person = $this->creator->initPerson();
    $number = $this->creator->initSocialSecurity();

    $person->setSocialSecurity($number);

    $this->assertNull($number->getId());

    $person->save();

    $this->assertNotNull($number->getId());
  }

  function testSaveParent()
  {
    $person = $this->creator->initPerson();
    $number = $this->creator->initSocialSecurity();
    
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
    $person = $this->creator->initPerson();
    $number1 = $this->creator->initSocialSecurity();
    
    $person->setSocialSecurity($number1);
    $person->save();

    $number2 = $this->creator->initSocialSecurity();
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
    $person = $this->creator->initPerson();
    
    $number1 = $this->creator->initSocialSecurity();
    
    $person->setSocialSecurity($number1);
    $person->save();

    $number2 = $this->creator->initSocialSecurity();
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
    $person = $this->creator->initPerson();
    $number = $this->creator->initSocialSecurity();
    $person->setSocialSecurity($number);
    $person->save();

    $number2 = lmbActiveRecord :: findById('SocialSecurityForTest', $number->getId());

    $person2 = $number2->getPerson();

    $this->assertEqual($person2->getId(), $person->getId());
    $this->assertEqual($person2->getName(), $person->getName());
  }

  function testGenericGetLoadsChildObject()
  {
    $person = $this->creator->initPerson();
    $number = $this->creator->initSocialSecurity();
    $person->setSocialSecurity($number);
    $person->save();

    $number2 = lmbActiveRecord :: findById('SocialSecurityForTest', $number->getId());

    $person2 = $number2->getPerson();

    $this->assertEqual($person2->getId(), $person->getId());
    $this->assertEqual($person2->getName(), $person->getName());
  }

  function testLoadChildObject()
  {
    $person = $this->creator->initPerson();
    $number = $this->creator->initSocialSecurity();
    $person->setSocialSecurity($number);
    $person_id = $person->save();

    $person2 = lmbActiveRecord :: findById('PersonForTest', $person_id);
    $number2 = $person2->getSocialSecurity();

    $this->assertEqual($person2->getId(), $person_id);
    $this->assertEqual($person2->getName(), $person->getName());
    $this->assertEqual($number2->getId(), $number->getId());
    $this->assertEqual($number2->getCode(), $number->getCode());
  }
  
  function testLoadNonExistingChildObject_ThrowsExceptionByDefault()
  {
    $person = $this->creator->initPerson();
    $number = $this->creator->initSocialSecurity();
    $person->setSocialSecurity($number);
    $person->save();
    
    $this->db->delete('social_security_for_test', 'id = '. $number->getId());

    $person2 = lmbActiveRecord :: findById('PersonForTest', $person->getId());

    try
    {
      $person2->getSocialSecurity();
      $this->assertTrue(false);
    }
    catch(lmbARNotFoundException $e)
    {
      $this->assertTrue(true);
    }
  }

  function testLoadNonExistingChildObject_NOT_ThrowsException_IfSpecialFlagUsedInRelationDefinition()
  {
    $person = $this->creator->initPerson();
    $number = $this->creator->initSocialSecurity();
    $person->setSocialSecurity($number);
    $person->save();
    
    $this->db->delete('social_security_for_test', 'id = '. $number->getId());

    $person2 = lmbActiveRecord :: findById('PersonForTestWithNotRequiredSocialSecurity', $person->getId());

    $this->assertNull($person2->getSocialSecurity());
  }

  function testGenericGetLoadsParentObject()
  {
    $person = $this->creator->initPerson();
    $number = $this->creator->initSocialSecurity();
    $person->setSocialSecurity($number);
    $person_id = $person->save();

    $person2 = lmbActiveRecord :: findById('PersonForTest', $person_id);
    $number2 = $person2->get('social_security');

    $this->assertEqual($person2->getId(), $person_id);
    $this->assertEqual($person2->getName(), $person->getName());
    $this->assertEqual($number2->getId(), $number->getId());
    $this->assertEqual($number2->getCode(), $number->getCode());
  }

  function testParentRemovalDeletesChildren()
  {
    $person = $this->creator->initPerson();
    $number = $this->creator->initSocialSecurity();
    $person->setSocialSecurity($number);

    $person_id = $person->save();
    $this->assertTrue($number_id = $number->getId());

    $person->destroy();

    $this->assertNull(lmbActiveRecord :: findFirst('SocialSecurityForTest', array('criteria' => lmbActiveRecord::getDefaultConnection()->quoteIdentifier("id") . '= ' . $number_id)));
    $this->assertNull(lmbActiveRecord :: findFirst('PersonForTest', array('criteria' => lmbActiveRecord::getDefaultConnection()->quoteIdentifier("id") . '= ' . $person_id)));
  }

  function testParentDeleteAllDeletesChildren()
  {
    $person = $this->creator->initPerson();
    $number = $this->creator->initSocialSecurity();
    $person->setSocialSecurity($number);
    $person_id = $person->save();
    
    $number_id = $number->getId();

    //this one should stay
    $untouched_number = $this->creator->initSocialSecurity();
    $untouched_number->save();

    lmbActiveRecord :: delete('PersonForTest');

    $this->assertNull(lmbActiveRecord :: findFirst('SocialSecurityForTest', array('criteria' => lmbActiveRecord::getDefaultConnection()->quoteIdentifier("id") . '= ' . $number_id)));
    $this->assertNull(lmbActiveRecord :: findFirst('PersonForTest', array('criteria' => lmbActiveRecord::getDefaultConnection()->quoteIdentifier("id") . '= ' . $person_id)));

    $number2 = lmbActiveRecord :: findById('SocialSecurityForTest', $untouched_number->getId());
    $this->assertEqual($number2->getCode(), $untouched_number->getCode());
  }

  function testParentRemovalWithNoCascadeDeleteChildren()
  {
    $person = new PersonForTestNoCascadeDelete();
    $person->setName('Jim');
    
    $number = $this->creator->initSocialSecurity();
    $person->setSocialSecurity($number);
    $person_id = $person->save();
    
    $this->assertTrue($number_id = $number->getId());

    $person->destroy();

    $ss2 = lmbActiveRecord :: findFirst('SocialSecurityForTest', array('criteria' => lmbActiveRecord::getDefaultConnection()->quoteIdentifier("id") . '= ' . $number_id));
    $this->assertEqual($ss2->getCode(), $number->getCode());
  }

  function testChildRemovalNullifyParentField()
  {
    $person = $this->creator->initPerson();
    $number = $this->creator->initSocialSecurity();
    $person->setSocialSecurity($number);
    $number->setPerson($person);
    $person->save();

    $number->destroy();

    $person2 = new PersonForTest($person->getId());
    $this->assertNull($person2->get('ss_id'));
  }

  function testChildRemovalWithRequiredObjectInParentRelationDefinitionThrowsValidationException()
  {
    $number = $this->creator->initSocialSecurity();

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
    $person = $this->creator->initPerson();
    $number = $this->creator->initSocialSecurity();
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
    $person = $this->creator->initPerson();
    $number = $this->creator->initSocialSecurity();
    $person->setSocialSecurity($number);
    $person->save();

    $source = array('name' => $person->getName());

    $person2 = new PersonForTest($person->getid());
    $person2->save();

    $this->assertEqual($person2->getName(), $person->getName());
    $this->assertEqual($person2->getSocialSecurity()->getCode(), $number->getCode());
  }
}


