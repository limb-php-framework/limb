<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbActiveRecordOneToOneRelationsTest.class.php 4984 2007-02-08 15:35:02Z pachanga $
 * @package    active_record
 */
lmb_require('limb/active_record/src/lmbActiveRecord.class.php');
lmb_require('limb/dbal/src/lmbSimpleDb.class.php');

class PersonForTest extends lmbActiveRecord
{
  protected $_has_one = array('social_security' => array('field' => 'ss_id',
                                                         'class' => 'SocialSecurityForTest',
                                                         'can_be_null' => true));
}

class PersonForTestNoCascadeDelete extends lmbActiveRecord
{
  protected $_db_table_name = 'person_for_test';
  protected $_has_one = array('social_security' => array('field' => 'ss_id',
                                                         'class' => 'SocialSecurityForTest',
                                                         'can_be_null' => true,
                                                         'cascade_delete' => false));
}

class SocialSecurityForTest extends lmbActiveRecord
{
  protected $_belongs_to = array('person' => array('field' => 'ss_id',
                                                   'class' => 'PersonForTest'));
}

class lmbActiveRecordOneToOneRelationsTest extends UnitTestCase
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

  function testParentRemovalWithoutChildren()
  {
    $person = new PersonForTest();
    $person->setName('Jim');
    $person->save();

    $person->destroy();
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

?>
