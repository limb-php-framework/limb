<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/constructor/tests/cases/lmbConstructorUnitTestCase.class.php');
lmb_require('limb/constructor/src/lmbModelConstructor.class.php');
lmb_require('limb/cli/src/lmbCliOutput.class.php');

class lmbModelConstructorTest extends lmbConstructorUnitTestCase
{
  /**
   * @var lmbDefaultModelConstructor
   */
  protected $model_constructor;

  function _createModelAndIncludeThem($table_name, $object_name)
  {
    $table = $this->conn->getDatabaseInfo()->getTable($table_name);
    $model_constructor = new lmbModelConstructor(
      new lmbProjectConstructor($this->dir_for_test_case, new lmbCliOutput()),
      $this->conn->getDatabaseInfo(),
      $table,
      $object_name
    );
    $model_constructor->create();

    foreach(lmb_glob($this->dir_for_test_case.'/src/model/*.class.php') as $file)
    {
      lmb_require($file);
    }
  }

  function testGetModelFileName()
  {
    $table = $this->conn->getDatabaseInfo()->getTable('lecture');
    $model_constructor = new lmbModelConstructor(
      new lmbProjectConstructor($this->dir_for_test_case, new lmbCliOutput()),
      $this->conn->getDatabaseInfo(),
      $table
    );
    $model_file_name = $model_constructor->getModelFileName();
    $this->assertEqual('Lecture.class.php', $model_file_name);
  }

  function testCreate_OneToOne()
  {
    $this->_createModelAndIncludeThem('social_security', 'SocialSecurity');
    $this->_createModelAndIncludeThem('person', 'Person');

    $ss = new SocialSecurity();
    $ss->setCode($ss_code = 42);
    $ss->save();

    $person = new Person();
    $person->setName($person_name = 'Vasya');
    $person->setSocialSecurity($ss);
    $person->save();

    $loaded_ss = lmbActiveRecord::findById('SocialSecurity', $ss->getId());
    $this->assertEqual($loaded_ss->getCode(), $ss_code);
    $this->assertEqual($loaded_ss->getPerson()->getId(), $person->getId());

    $loaded_person = lmbActiveRecord::findById('Person', $person->getId());
    $this->assertEqual($loaded_person->getSocialSecurity()->getId(), $ss->getId());
  }

  function testCreate_OneToMany()
  {
    $this->_createModelAndIncludeThem('lecture', 'Lecture');
    $this->_createModelAndIncludeThem('course', 'Course');

    $course = new Course();
    $course->setTitle($course_title = 'bar');
    $course->save();

    $lecture = new Lecture();
    $lecture->setTitle($lecture_title = 'foo');
    $lecture->setCourse($course);
    $lecture->save();

    $loaded_lecture = lmbActiveRecord::findById('Lecture', $lecture->getId());
    $this->assertEqual($loaded_lecture->getTitle(), $lecture_title);
    $this->assertEqual($loaded_lecture->getCourse()->getId(), $course->getId());

    $loaded_course = lmbActiveRecord::findById('Course', $course->getId());
    $this->assertEqual($loaded_course->getLectures()->at(0)->getId(), $lecture->getId());
  }

  function testCreate_ManyToMany()
  {
    $this->_createModelAndIncludeThem('user', 'User');
    $this->_createModelAndIncludeThem('group', 'Group');

    $user = new User();
    $user->setFirstName($user_first_name = 'Vasya');
    $user->save();

    $group = new Group();
    $group->setTitle($group_title = 'Moderasti');
    $group->getUsers()->add($user);
    $group->save();

    $loaded_user = lmbActiveRecord::findById('User', $user->getId());
    $this->assertEqual($loaded_user->getFirstName(), $user_first_name);
    $this->assertEqual($loaded_user->getGroups()->at(0)->getId(), $group->getId());

    $loaded_group = lmbActiveRecord::findById('Group', $group->getId());
    $this->assertEqual($loaded_group->getUsers()->at(0)->getId(), $user->getId());
  }

  function testCreateWithLazyAttributesProperty()
  {
    $this->_createModelAndIncludeThem('document', 'Document');
    $expected_lazy_attributes = array('description', 'content');

    $doc = new Document();
    $lazy_attributes = $doc->getLazyAttributes();

    $this->assertIdentical($lazy_attributes, $expected_lazy_attributes);
  }
}