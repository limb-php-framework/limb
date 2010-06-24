<?php
class TestOneTableObject extends lmbActiveRecord
{
  protected $_db_table_name = 'test_one_table_object';
}

class TestOneTableObjectFailing extends lmbActiveRecord
{
  var $fail;
  protected $_db_table_name = 'test_one_table_object';

  protected function _onAfterSave()
  {
    if(is_object($this->fail))
      throw $this->fail;
  }
}

class LazyTestOneTableObject extends lmbActiveRecord
{
  protected $_db_table_name = 'test_one_table_object';
  protected $_lazy_attributes = array('annotation', 'content');
}

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

class PersonForLazyAttributesTest extends lmbActiveRecord
{
  protected $_db_table_name = 'person_for_test';
  protected $_has_one = array('lazy_object' => array('field' => 'ss_id',
                                                     'class' => 'LazyTestOneTableObject',
                                                     'can_be_null' => true));
  protected $_lazy_attributes = array('name');
}

class SocialSecurityForTest extends lmbActiveRecord
{
  protected $_belongs_to = array('person' => array('field' => 'ss_id',
                                                   'class' => 'PersonForTest'));
}

class ProgramForTest extends lmbActiveRecord
{
  protected $_db_table_name = 'program_for_test';

  protected $_has_many = array('courses' => array('field' => 'program_id',
                                                  'class' => 'CourseForTest'),
    
                               'cached_lectures' => array('field' => 'program_id',
                                                          'class' => 'LectureForTest'));
}

class CourseForTest extends lmbActiveRecord
{
  protected $_db_table_name = 'course_for_test';
  protected $_has_many = array('lectures' => array('field' => 'course_id',
                                                   'class' => 'LectureForTest'),
                               'alt_lectures' => array('field' => 'alt_course_id',
                                                       'class' => 'LectureForTest'),
                               'foo_lectures' => array('field' => 'course_id',
                                                       'class' => 'LectureForTest',
                                                        'criteria'=>'lecture_for_test.title like "foo%"'));

  protected $_many_belongs_to = array('program' => array('field' => 'program_id',
                                                         'class' => 'ProgramForTest',
                                                         'can_be_null' => true));
  
  public $save_calls = 0;

  function save()
  {
    parent :: save();
    $this->save_calls++;
  }
}

class LectureForTest extends lmbActiveRecord
{
  protected $_db_table_name = 'lecture_for_test';
  protected $_many_belongs_to = array('course' => array('field' => 'course_id',
                                                        'class' => 'CourseForTest'),
                                      'alt_course' => array('field' => 'alt_course_id',
                                                            'class' => 'CourseForTest',
                                                            'can_be_null' => true),
                                      'cached_program' => array('field' => 'program_id',
                                                                'class' => 'ProgramForTest'));
  protected $_test_validator;

  function setValidator($validator)
  {
    $this->_test_validator = $validator;
  }

  function _createValidator()
  {
    if($this->_test_validator)
      return $this->_test_validator;

    return parent :: _createValidator();
  }
}

class GroupForTest extends lmbActiveRecord
{
  protected $_db_table_name = 'group_for_test';

  protected $_has_many_to_many = array('users' => array('field' => 'group_id',
                                                        'foreign_field' => 'user_id',
                                                        'table' => 'user_for_test2group_for_test',
                                                        'class' => 'UserForTest'));

  protected $_test_validator;

  function setValidator($validator)
  {
    $this->_test_validator = $validator;
  }

  function _createValidator()
  {
    if($this->_test_validator)
      return $this->_test_validator;

    return parent :: _createValidator();
  }
}

class UserForTest extends lmbActiveRecord
{
  protected $_db_table_name = 'user_for_test';

  protected $_has_many_to_many = array('groups' => array('field' => 'user_id',
                                                         'foreign_field' => 'group_id',
                                                         'table' => 'user_for_test2group_for_test',
                                                         'class' => 'GroupForTest'),
                                       'cgroups' => array('field' => 'user_id',
                                                         'foreign_field' => 'group_id',
                                                         'table' => 'user_for_test2group_for_test',
                                                         'class' => 'GroupForTest',
                                                         'criteria' =>'group_for_test.title="condition"'
                                                                ));

  protected $_has_one = array('linked_object' => array('field' => 'linked_object_id',
                                                       'class' => 'TestOneTableObject',
                                                       'can_be_null' => true));
}

class lmbARTestingObjectMother
{
  function initOneTableObject($ordr = '')
  {
    $object = new TestOneTableObject();
    $object->set('annotation', 'Annotation ' . rand(0, 1000));
    $object->set('content', 'Content ' . rand(0, 1000));
    $object->set('news_date', date("Y-m-d", time()));
    $ordr = $ordr ? $ordr : rand(0, 1000);
    $object->set('ordr', $ordr);
    return $object;
  }
  
  function createOneTableObject($ordr = '')
  {
    $object = $this->initOneTableObject($ordr);
    $object->save();
    return $object;
  }
  
  function initPerson()
  {
    $person = new PersonForTest();
    $person->setName('Person_' . rand(0, 1000));
    return $person;
  }
  
  function createPerson()
  {
    $person = $this->initPerson();

    $number = $this->createSocialSecurity($person);
    $person->setSocialSecurity($number);
    $person->save();
    return $person;
  }
  
  function initSocialSecurity()
  {
    $number = new SocialSecurityForTest();
    $number->setCode(rand(0,1000));
    return $number;
  }
  
  function createSocialSecurity($person)
  {
    $number = $this->initSocialSecurity();
    $number->setPerson($person);
    return $number; 
  }
  
  function createCourse($program = null)
  {
    $course = new CourseForTest();
    $course->setTitle('Course_'. rand(0, 100));
    
    if($program)
      $course->setProgram($program);
    
    $course->save();
    return $course;
  }
  
  function createProgram()
  {
    $program = new ProgramForTest();
    $program->setTitle('Program_'. rand(0, 100));
    $program->save();
    return $program;
  }

  function createLecture($course, $alt_course = null, $title = '')
  {
    $lecture = new LectureForTest();
    $title = $title ? $title : 'Lecture_'. rand(0, 100);
    $lecture->setTitle($title);
    $lecture->setCourse($course);
    
    if($alt_course)
      $lecture->setAltCourse($alt_course);
      
    $lecture->save();
    return $lecture;
  }
  
  function initUser($linked_object = null)
  {
    $user = new UserForTest();
    $user->setFirstName('User_' . rand(0, 1000));
    
    if($linked_object)
      $user->setLinkedObject($linked_object);

    return $user;
  }
  
  function createUser($linked_object = null)
  {
    $user = $this->initUser($linked_object);
    $user->save();
    return $user;
  }
  
  function initGroup($title = '')
  {
    $group = new GroupForTest();
    $title = $title ? $title : 'Group_' . rand(0, 1000);
    $group->setTitle($title);
    return $group;
  }
  
  function createGroup($title = '')
  {
    $group = $this->initGroup($title);
    $group->save();
    return $group;
  }
}
