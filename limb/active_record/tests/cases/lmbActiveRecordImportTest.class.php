<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbActiveRecordImportTest.class.php 4984 2007-02-08 15:35:02Z pachanga $
 * @package    active_record
 */
lmb_require('limb/active_record/src/lmbActiveRecord.class.php');
lmb_require('limb/dbal/src/lmbSimpleDb.class.php');
lmb_require('limb/datasource/src/lmbDataspace.class.php');
require_once(dirname(__FILE__) . '/lmbActiveRecordTest.class.php');
require_once(dirname(__FILE__) . '/lmbActiveRecordOneToManyRelationsTest.class.php');
require_once(dirname(__FILE__) . '/lmbActiveRecordOneToOneRelationsTest.class.php');
require_once(dirname(__FILE__) . '/lmbActiveRecordManyToManyRelationsTest.class.php');
require_once(dirname(__FILE__) . '/lmbActiveRecordValueObjectTest.class.php');
require_once(dirname(__FILE__) . '/lmbActiveRecordAttributesLazyLoadingTest.class.php');

class lmbActiveRecordImportTest extends UnitTestCase
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
    lmbActiveRecord :: delete('TestOneTableObject');
    lmbActiveRecord :: delete('CourseForTest');
    lmbActiveRecord :: delete('LectureForTest');
    lmbActiveRecord :: delete('GroupForTest');
    lmbActiveRecord :: delete('UserForTest');
    lmbActiveRecord :: delete('LessonForTest');
    lmbActiveRecord :: delete('PersonForTest');
    lmbActiveRecord :: delete('SocialSecurityForTest');
  }

  function testImportingObjectCallsItsExportMethod()
  {
    $object = new TestOneTableObject();
    $object->import(new lmbDataspace(array('annotation' => 'Some annotation')));
    $this->assertEqual($object->getAnnotation(), 'Some annotation');
  }

  function testImportNewActiveRecord()
  {
    $object1 = new TestOneTableObject();
    $object1->setId(100); //note this
    $object1->setAnnotation($annotation = 'Some annotation');

    $object2 = new TestOneTableObject();
    $object2->import($object1);
    $this->assertEqual($object2->getId(), $object1->getId());
    $this->assertEqual($object2->getAnnotation(), $annotation);
    $this->assertTrue($object2->isNew());
    $this->assertTrue($object2->isDirty());
  }

  function testImportExistingActiveRecord()
  {
    $object1 = new TestOneTableObject();
    $object1->setAnnotation($annotation = 'Some annotation');
    $object1->save();

    $object2 = new TestOneTableObject();
    $object2->import($object1);
    $this->assertEqual($object2->getId(), $object1->getId());
    $this->assertEqual($object2->getAnnotation(), $annotation);
    $this->assertFalse($object2->isNew());
    $this->assertTrue($object2->isDirty());
  }

  function testImportActiveRecordWithLazyAttributes()
  {
    $object = new LazyTestOneTableObject();
    $object->setContent($content = 'Some content');
    $object->setAnnotation($annotation = 'Some annotation');
    $object->save();

    $object1 = new LazyTestOneTableObject();
    $object2 = new LazyTestOneTableObject($object->getId());

    $object1->import($object2);
    $this->assertFalse($object1->hasAttribute('annotation'));
    $this->assertEqual($object1->getAnnotation(), $annotation);
    $this->assertFalse($object1->hasAttribute('content'));
    $this->assertEqual($object1->getContent(), $content);
  }

  function testImportOverwritesIdOfNewObject()
  {
    $object = new TestOneTableObject();
    $object->setId(1);

    $source = array('id' => 1000,
                    'annotation' => 'Some annotation',
                    'content' => 'Some content',
                    );

    $object->import($source);
    $this->assertEqual($object->getId(), 1000);
    $this->assertEqual($object->getAnnotation(), 'Some annotation');
    $this->assertEqual($object->getContent(), 'Some content');
    $this->assertNull($object->getJunk());
  }

  function testImportPreservesIdOfExistingObject()
  {
    $object = new TestOneTableObject();
    $object->setAnnotation('Initial annotation');
    $object->save();
    $id = $object->getId();

    $source = array('id' => 10000,
                    'annotation' => 'Some annotation',
                    'content' => 'Some content',
                    );

    $object->import($source);
    $this->assertEqual($object->getId(), $id);
    $this->assertNotEqual($object->getId(), 1000);// just one extra check
    $this->assertEqual($object->getAnnotation(), 'Some annotation');
    $this->assertEqual($object->getContent(), 'Some content');
  }

  function testPassingArrayToConstructorCallsImport()
  {
    $source = array('id' => 1000,
                    'annotation' => 'Some annotation',
                    'content' => 'Some content',
                    );

    $object = new TestOneTableObject($source);
    $this->assertEqual($object->getId(), 1000);
    $this->assertEqual($object->getAnnotation(), 'Some annotation');
    $this->assertEqual($object->getContent(), 'Some content');
  }

  function testImportWhereOne2ManyCollectionIsArrayOfIds()
  {
    $course = new CourseForTest();
    $course->setTitle('Some course');

    $l1 = new LectureForTest();
    $l1->setTitle('Physics');
    $l2 = new LectureForTest();
    $l2->setTitle('Math');

    $course->addToLectures($l1);
    $course->addToLectures($l2);

    $course->save();

    $source = array('title' => $course->getTitle(),
                    'lectures' => array($l1->getId(), $l2->getId()));

    $course2 = new CourseForTest();
    $course2->import($source);
    $this->assertEqual($course2->getTitle(), $course->getTitle());
    $this->assertEqual($course2->getLectures()->count(), 2);
    $this->assertEqual($course2->getLectures()->at(0)->getTitle(), $l1->getTitle());
    $this->assertEqual($course2->getLectures()->at(1)->getTitle(), $l2->getTitle());
  }

  function testImportWhereOne2ManyCollectionIsMixedArray()
  {
    $course = new CourseForTest();
    $course->setTitle('Some course');

    $l1 = new LectureForTest();
    $l1->setTitle('Physics');
    $l2 = new LectureForTest();
    $l2->setTitle('Math');

    $course->addToLectures($l1);
    $course->addToLectures($l2);

    $course->save();

    $source = array('title' => $course->getTitle(),
                    'lectures' => array($l1->getId(), $l2));

    $course2 = new CourseForTest();
    $course2->import($source);
    $this->assertEqual($course2->getTitle(), $course->getTitle());
    $this->assertEqual($course2->getLectures()->count(), 2);
    $this->assertEqual($course2->getLectures()->at(0)->getTitle(), $l1->getTitle());
    $this->assertEqual($course2->getLectures()->at(1)->getTitle(), $l2->getTitle());
  }

  function testImportResetsExistingOne2ManyCollection()
  {
    $course = new CourseForTest();
    $course->setTitle('Some course');

    $l1 = new LectureForTest();
    $l1->setTitle('Physics');
    $l2 = new LectureForTest();
    $l2->setTitle('Math');

    $course->addToLectures($l1);
    $course->addToLectures($l2);

    $course->save();

    $source = array('title' => $course->getTitle(),
                    'lectures' => array($l2->getId()));

    $course2 = new CourseForTest($course->getId());

    $course2->import($source);
    $this->assertEqual($course2->getTitle(), $course->getTitle());
    $this->assertEqual($course2->getLectures()->count(), 1);
    $this->assertEqual($course2->getLectures()->at(0)->getTitle(), $l2->getTitle());
  }

  function testImportResetsExistingMany2ManyCollection()
  {
    $group = new GroupForTest();
    $group->setTitle('Some group');

    $u1 = new UserForTest();
    $u1->setFirstName('Bob');
    $u2 = new UserForTest();
    $u2->setFirstName('John');

    $group->addToUsers($u1);
    $group->addToUsers($u2);

    $group->save();

    $source = array('title' => $group->getTitle(),
                    'users' => array($u2->getId()));

    $group2 = new GroupForTest($group->getId());
    $group2->import($source);
    $this->assertEqual($group2->getTitle(), $group->getTitle());
    $this->assertEqual($group2->getUsers()->count(), 1);
    $this->assertEqual($group2->getUsers()->at(0)->getFirstName(), $u2->getFirstName());
  }

  function testImportWhereOne2ManyParentIsNumericId()
  {
    $course = new CourseForTest();
    $course->setTitle('Some course');

    $l = new LectureForTest();
    $l->setTitle('Physics');
    $l->setCourse($course);

    $l->save();

    $source = array('title' => $l->getTitle(),
                    'course' => $course->getId());

    $l2 = new LectureForTest();
    $l2->import($source);

    $this->assertEqual($l2->getTitle(), $l->getTitle());
    $this->assertEqual($l2->getCourse()->getTitle(), $course->getTitle());
  }

  function testImportWhereOne2ManyParentIsObject()
  {
    $course = new CourseForTest();
    $course->setTitle('Some course');

    $l = new LectureForTest();
    $l->setTitle('Physics');
    $l->setCourse($course);

    $l->save();

    $source = array('title' => $l->getTitle(),
                    'course' => $course);

    $l2 = new LectureForTest();
    $l2->import($source);
    $this->assertEqual($l2->getTitle(), $l->getTitle());
    $this->assertEqual($l2->getCourse()->getTitle(), $course->getTitle());
  }

  function testImportWhereMany2ManyCollectionIsArrayOfIds()
  {
    $user1 = new UserForTest();
    $user1->setFirstName('Bob');

    $g1 = new GroupForTest();
    $g1->setTitle('vp1');
    $g2 = new GroupForTest();
    $g2->setTitle('vp1');

    $user1->addToGroups($g1);
    $user1->addToGroups($g2);
    $user1->save();

    $source = array('first_name' => $user1->getFirstName(),
                    'groups' => array($g1->getId(), $g2->getId()));

    $user2 = new UserForTest();
    $user2->import($source);
    $this->assertEqual($user2->getFirstName(), $user1->getFirstName());
    $this->assertEqual($user2->getGroups()->count(), 2);
    $this->assertEqual($user2->getGroups()->at(0)->getTitle(), $g1->getTitle());
    $this->assertEqual($user2->getGroups()->at(1)->getTitle(), $g2->getTitle());
  }

  function testImportWhereMany2ManyCollectionIsMixedArray()
  {
    $user1 = new UserForTest();
    $user1->setFirstName('Bob');

    $g1 = new GroupForTest();
    $g1->setTitle('vp1');
    $g2 = new GroupForTest();
    $g2->setTitle('vp1');

    $user1->addToGroups($g1);
    $user1->addToGroups($g2);
    $user1->save();

    $source = array('first_name' => $user1->getFirstName(),
                    'groups' => array($g1->getId(), $g2));

    $user2 = new UserForTest();
    $user2->import($source);
    $this->assertEqual($user2->getFirstName(), $user1->getFirstName());
    $this->assertEqual($user2->getGroups()->count(), 2);
    $this->assertEqual($user2->getGroups()->at(0)->getTitle(), $g1->getTitle());
    $this->assertEqual($user2->getGroups()->at(1)->getTitle(), $g2->getTitle());
  }

  function testImportOne2OneWhereParentIsNumericId()
  {
    $person = new PersonForTest();
    $person->setName('Jim');
    $number = new SocialSecurityForTest();
    $number->setCode('099123');
    $person->setSocialSecurity($number);
    $person->save();

    $source = array('code' => $number->getCode(),
                    'person' => $person->getId());

    $number2 = new SocialSecurityForTest();
    $number2->import($source);
    $this->assertEqual($number2->getCode(), $number->getCode());
    $this->assertEqual($number2->getPerson()->getName(), $person->getName());
  }

  function testImportOne2OneWhereParentIsObject()
  {
    $person = new PersonForTest();
    $person->setName('Jim');
    $number = new SocialSecurityForTest();
    $number->setCode('099123');
    $person->setSocialSecurity($number);
    $person->save();

    $source = array('code' => $number->getCode(),
                    'person' => $person);

    $number2 = new SocialSecurityForTest();
    $number2->import($source);
    $this->assertEqual($number2->getCode(), $number->getCode());
    $this->assertEqual($number2->getPerson()->getName(), $person->getName());
  }

  function testImportOne2OneWhereChildIsId()
  {
    $person = new PersonForTest();
    $person->setName('Jim');
    $number = new SocialSecurityForTest();
    $number->setCode('099123');
    $person->setSocialSecurity($number);
    $person->save();

    $source = array('name' => $person->getName(),
                    'social_security' => $number->getId());

    $person2 = new PersonForTest();
    $person2->import($source);
    $this->assertEqual($person2->getName(), $person->getName());
    $this->assertEqual($person2->getSocialSecurity()->getCode(), $number->getCode());
  }

  function testImportOne2OneWhereChildIsObject()
  {
    $person = new PersonForTest();
    $person->setName('Jim');
    $number = new SocialSecurityForTest();
    $number->setCode('099123');
    $person->setSocialSecurity($number);
    $person->save();

    $source = array('name' => $person->getName(),
                    'social_security' => $number);

    $person2 = new PersonForTest();
    $person2->import($source);
    $this->assertEqual($person2->getName(), $person->getName());
    $this->assertEqual($person2->getSocialSecurity()->getCode(), $number->getCode());
  }

  function testImportNullEntity()
  {
    $person = new PersonForTest();
    $person->setName('Jim');
    $number = new SocialSecurityForTest();
    $number->setCode('099123');
    $person->setSocialSecurity($number);
    $person->save();

    $source = array('name' => $person->getName(),
                    'social_security' => null);

    $person2 = clone $person;
    $person2->import($source);
    $this->assertEqual($person2->getName(), $person->getName());
    $this->assertNull($person2->getSocialSecurity());
  }

  function testImportNullEntityString()
  {
    $person = new PersonForTest();
    $person->setName('Jim');
    $number = new SocialSecurityForTest();
    $number->setCode('099123');
    $person->setSocialSecurity($number);
    $person->save();

    $source = array('name' => $person->getName(),
                    'social_security' => 'null');

    $person2 = clone $person;
    $person2->import($source);
    $this->assertEqual($person2->getName(), $person->getName());
    $this->assertNull($person2->getSocialSecurity());
  }

  function testImportWithValueObject()
  {
    $lesson = new LessonForTest();

    $lesson->import(array('date_start' => $v1 = time() - 100,
                          'date_end' => $v2 = time() + 100));

    $this->assertEqual($lesson->getDateStart()->getValue(), $v1);
    $this->assertEqual($lesson->getDateEnd()->getValue(), $v2);
  }
}

?>
