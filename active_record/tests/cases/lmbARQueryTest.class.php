<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
 
class lmbARQueryTest extends lmbARBaseTestCase
{
  protected $tables_to_cleanup = array('test_one_table_object',
                                       'program_for_test',
                                       'person_for_test', 
                                       'social_security_for_test',
                                       'lecture_for_test',
                                       'course_for_test');
  
  function testSimpleFetch()
  {
    $object1 = $this->creator->createOneTableObject();
    $object2 = $this->creator->createOneTableObject();
    
    $query = lmbARQuery :: create('TestOneTableObject', array(), $this->conn);
    $iterator = $query->fetch();
    $arr = $iterator->getArray();
    
    $this->assertIsA($arr[0], 'TestOneTableObject');
    $this->assertEqual($arr[0]->getAnnotation(), $object1->getAnnotation());
    $this->assertIsA($arr[1], 'TestOneTableObject');
    $this->assertEqual($arr[1]->getAnnotation(), $object2->getAnnotation());
  }

  function testSimpleFetch_WithSort()
  {
    $object1 = $this->creator->createOneTableObject(10);
    $object2 = $this->creator->createOneTableObject(20);
    
    $query = lmbARQuery :: create('TestOneTableObject', array('sort' => array('ordr' => 'DESC')), $this->conn);
    $iterator = $query->fetch();
    $iterator->sort(array('id' => 'ASC'));
    $arr = $iterator->getArray();
    
    $this->assertIsA($arr[0], 'TestOneTableObject');
    $this->assertEqual($arr[0]->getAnnotation(), $object1->getAnnotation());
    $this->assertIsA($arr[1], 'TestOneTableObject');
    $this->assertEqual($arr[1]->getAnnotation(), $object2->getAnnotation());
  }
  
  function testGetRecordSetWIthSort()
  {
    $object1 = $this->creator->createOneTableObject(10);
    $object2 = $this->creator->createOneTableObject(20);
    
    $query = lmbARQuery :: create('TestOneTableObject', array('sort' => array('ordr' => 'DESC')), $this->conn);
    $iterator = $query->getRecordSet();
    $arr = $iterator->getArray();
    
    $this->assertEqual($arr[0]->get('annotation'), $object2->getAnnotation());
    $this->assertEqual($arr[1]->get('annotation'), $object1->getAnnotation());
  }
  
  function testFetch_Join_RelatedHasOneObject()
  {
    $person1 = $this->creator->createPerson();
    $person2 = $this->creator->createPerson();
    
    $this->conn->resetStats();
    
    $query = lmbARQuery :: create('PersonForTest', array(), $this->conn);
    $query->eagerJoin('social_security');
    $iterator = $query->fetch();
    $arr = $iterator->getArray();
    
    $this->assertEqual($this->conn->countQueries(), 1);

    //make sure we really eager fetching
    $this->db->delete('social_security_for_test');

    $this->conn->resetStats();
    
    $this->assertIsA($arr[0], 'PersonForTest');
    $this->assertEqual($arr[0]->getName(), $person1->getName());
    $this->assertIsA($arr[0]->getSocialSecurity(), 'SocialSecurityForTest');
    $this->assertEqual($arr[0]->getSocialSecurity()->getCode(), $person1->getSocialSecurity()->getCode());
    
    $this->assertIsA($arr[1], 'PersonForTest');
    $this->assertEqual($arr[1]->getName(), $person2->getName());
    $this->assertIsA($arr[1]->getSocialSecurity(), 'SocialSecurityForTest');
    $this->assertEqual($arr[1]->getSocialSecurity()->getCode(), $person2->getSocialSecurity()->getCode());

    $this->assertEqual($this->conn->countQueries(), 0);
  }
  
  function testFetch_Join_RelatedBelongsToObject()
  {
    $person1 = $this->creator->createPerson();
    $ss1 = $person1->getSocialSecurity();
    $person2 = $this->creator->createPerson();
    $ss2 = $person2->getSocialSecurity();
    
    $this->conn->resetStats();
    
    $query = lmbARQuery :: create('SocialSecurityForTest', array(), $this->conn);
    $query->eagerJoin('person');
    $iterator = $query->fetch();
    $arr = $iterator->getArray();

    $this->assertEqual($this->conn->countQueries(), 1);
    
    //make sure we really eager fetching
    $this->db->delete('person_for_test');

    $this->conn->resetStats();
    
    $this->assertIsA($arr[0], 'SocialSecurityForTest');
    $this->assertEqual($arr[0]->getCode(), $ss1->getCode());
    $this->assertIsA($arr[0]->getPerson(), 'PersonForTest');
    $this->assertEqual($arr[0]->getPerson()->getName(), $person1->getName());
    
    $this->assertIsA($arr[1], 'SocialSecurityForTest');
    $this->assertEqual($arr[1]->getCode(), $ss2->getCode());
    $this->assertIsA($arr[1]->getPerson(), 'PersonForTest');
    $this->assertEqual($arr[1]->getPerson()->getName(), $person2->getName());

    $this->assertEqual($this->conn->countQueries(), 0);
  }

  function testFetch_Join_RelatedManyBelongsToObject()
  {
    $course1 = $this->creator->createCourse();
    $course2 = $this->creator->createCourse();
    $lecture1 = $this->creator->createLecture($course1);
    $lecture2 = $this->creator->createLecture($course1);
    $lecture3 = $this->creator->createLecture($course2);
    
    $this->conn->resetStats();
    
    $query = lmbARQuery :: create('LectureForTest', array(), $this->conn);
    $query->eagerJoin('course');
    $iterator = $query->fetch();
    $arr = $iterator->getArray();
    
    $this->assertEqual($this->conn->countQueries(), 1);

    //make sure we really eager fetching
    $this->db->delete('course_for_test');
    
    $this->conn->resetStats();
    
    $this->assertIsA($arr[0], 'LectureForTest');
    $this->assertEqual($arr[0]->getTitle(), $lecture1->getTitle());
    $this->assertIsA($arr[0]->getCourse(), 'CourseForTest');
    $this->assertEqual($arr[0]->getCourse()->getTitle(), $course1->getTitle());
    
    $this->assertIsA($arr[1], 'LectureForTest');
    $this->assertEqual($arr[1]->getTitle(), $lecture2->getTitle());
    $this->assertIsA($arr[1]->getCourse(), 'CourseForTest');
    $this->assertEqual($arr[1]->getCourse()->getTitle(), $course1->getTitle());
    
    $this->assertIsA($arr[2], 'LectureForTest');
    $this->assertEqual($arr[2]->getTitle(), $lecture3->getTitle());
    $this->assertIsA($arr[2]->getCourse(), 'CourseForTest');
    $this->assertEqual($arr[2]->getCourse()->getTitle(), $course2->getTitle());

    $this->assertEqual($this->conn->countQueries(), 0);
  }

  function testFetch_Attach_RelatedHasOneObjects()
  {
    $person1 = $this->creator->createPerson();
    $person2 = $this->creator->createPerson();
    
    $this->conn->resetStats();
    
    $query = lmbARQuery :: create('PersonForTest', array(), $this->conn);
    // note attach() has the same effect as join() but works in a different way - it produces another sql request 
    $iterator = $query->eagerAttach('social_security')->fetch();
    $arr = $iterator->getArray();
    
    $this->assertEqual($this->conn->countQueries(), 2);

    //make sure we really eager fetching
    $this->db->delete('social_security_for_test');
    
    $this->conn->resetStats();
    
    $this->assertIsA($arr[0], 'PersonForTest');
    $this->assertEqual($arr[0]->getName(), $person1->getName());
    $this->assertIsA($arr[0]->getSocialSecurity(), 'SocialSecurityForTest');
    $this->assertEqual($arr[0]->getSocialSecurity()->getCode(), $person1->getSocialSecurity()->getCode());
    
    $this->assertIsA($arr[1], 'PersonForTest');
    $this->assertEqual($arr[1]->getName(), $person2->getName());
    $this->assertIsA($arr[1]->getSocialSecurity(), 'SocialSecurityForTest');
    $this->assertEqual($arr[1]->getSocialSecurity()->getCode(), $person2->getSocialSecurity()->getCode());

    $this->assertEqual($this->conn->countQueries(), 0);
  }
  
  function testFetch_Attach_WhenEmptyRecordSet_ForHasOneRelation()
  {
    $this->conn->resetStats();
    
    $query = lmbARQuery :: create('PersonForTest', array(), $this->conn);
    // note attach() has the same effect as join() but workds is a different way - it produces another sql request 
    $iterator = $query->eagerAttach('social_security')->fetch();
    $arr = $iterator->getArray();
    $this->assertEqual(sizeof($arr), 0);
    
    $this->assertEqual($this->conn->countQueries(), 1);
  }
  
  function testFetch_Attach_WhenEmptyRecordSet_ForBelongsToRelation()
  {
    $this->conn->resetStats();
    
    $query = lmbARQuery :: create('SocialSecurityForTest', array(), $this->conn);
    // note attach() has the same effect as join() but workds is a different way - it produces another sql request 
    $iterator = $query->eagerAttach('person')->fetch();
    $arr = $iterator->getArray();
    $this->assertEqual(sizeof($arr), 0);
    
    $this->assertEqual($this->conn->countQueries(), 1);
  }  

  function testFetch_Attach_RelatedBelongsToObjects()
  {
    $id = $this->db->insert('person_for_test', array('id' => 100, 'name' => 'junky person'));
    
    $person1 = $this->creator->createPerson();
    $person2 = $this->creator->createPerson();
    
    $this->db->delete('person_for_test', $this->conn->quoteIdentifier("id") . '= ' . $id);
    
    $this->conn->resetStats();
    
    $query = lmbARQuery :: create('SocialSecurityForTest', array(), $this->conn);
    // note attach() has the same effect as join() but workds is a different way - it produces another sql request 
    $arr = $query->eagerAttach('person')->fetch()->getArray();

    $this->assertEqual($this->conn->countQueries(), 2);
    
    //make sure we really eager fetching
    $this->db->delete('person_for_test');

    $this->conn->resetStats();
    
    $this->assertIsA($arr[0], 'SocialSecurityForTest');
    $this->assertEqual($arr[0]->getCode(), $person1->getSocialSecurity()->getCode());
    $this->assertIsA($arr[0]->getPerson(), 'PersonForTest');
    $this->assertEqual($arr[0]->getPerson()->getName(), $person1->getName());
    
    $this->assertIsA($arr[1], 'SocialSecurityForTest');
    $this->assertEqual($arr[1]->getCode(), $person2->getSocialSecurity()->getCode());
    $this->assertIsA($arr[1]->getPerson(), 'PersonForTest');
    $this->assertEqual($arr[1]->getPerson()->getName(), $person2->getName());

    $this->assertEqual($this->conn->countQueries(), 0);
  }

  function testFetch_Attach_RelatedManyBelongsToObjects()
  {
   $course = $this->creator->createCourse();
    
    $alt_course1 = $this->creator->createCourse();
    $alt_course2 = $this->creator->createCourse();
    
    $lecture1 = $this->creator->createLecture($course, $alt_course1);
    $lecture2 = $this->creator->createLecture($course, $alt_course2);
    $lecture3 = $this->creator->createLecture($course, $alt_course1);
    
    $this->conn->resetStats();
    
    $query = lmbARQuery :: create('LectureForTest', array(), $this->conn);
    $arr = $query->eagerAttach('course')->eagerAttach('alt_course')->fetch()->getArray();
    
    $this->assertEqual($this->conn->countQueries(), 3);
    
    //make sure we really eager fetching
    $this->db->delete('course_for_test');

    $this->conn->resetStats();
    
    $this->assertIsA($arr[0], 'LectureForTest');
    $this->assertEqual($arr[0]->getTitle(), $lecture1->getTitle());
    $this->assertIsA($arr[0]->getCourse(), 'CourseForTest');
    $this->assertEqual($arr[0]->getCourse()->getTitle(), $course->getTitle());
    $this->assertIsA($arr[0]->getAltCourse(), 'CourseForTest');
    $this->assertEqual($arr[0]->getAltCourse()->getTitle(), $alt_course1->getTitle());

    $this->assertIsA($arr[1], 'LectureForTest');
    $this->assertEqual($arr[1]->getTitle(), $lecture2->getTitle());
    $this->assertIsA($arr[1]->getCourse(), 'CourseForTest');
    $this->assertEqual($arr[1]->getCourse()->getTitle(), $course->getTitle());
    $this->assertIsA($arr[1]->getAltCourse(), 'CourseForTest');
    $this->assertEqual($arr[1]->getAltCourse()->getTitle(), $alt_course2->getTitle());

    $this->assertIsA($arr[2], 'LectureForTest');
    $this->assertEqual($arr[2]->getTitle(), $lecture3->getTitle());
    $this->assertIsA($arr[2]->getCourse(), 'CourseForTest');
    $this->assertEqual($arr[2]->getCourse()->getTitle(), $course->getTitle());
    $this->assertIsA($arr[2]->getAltCourse(), 'CourseForTest');
    $this->assertEqual($arr[2]->getAltCourse()->getTitle(), $alt_course1->getTitle()); 
    
    $this->assertEqual($this->conn->countQueries(), 0);
  }

  function testFetch_Attach_RelatedHasMany()
  {
    $course1 = $this->creator->createCourse();
    $course2 = $this->creator->createCourse();
    
    $lecture1 = $this->creator->createLecture($course1, null, 'ZZZ');
    $lecture2 = $this->creator->createLecture($course2, null, 'CCC');
    $lecture3 = $this->creator->createLecture($course1, null, 'AAA');
    $lecture4 = $this->creator->createLecture($course1, null, 'BBB');
    
    $this->conn->resetStats();
    
    $query = lmbARQuery :: create('CourseForTest', array(), $this->conn);
    $arr = $query->eagerAttach('lectures', array('sort' => array('title' => 'ASC')))->fetch()->getArray();
    
    $this->assertEqual($this->conn->countQueries(), 2);
    
    //make sure we really eager fetching
    $this->db->delete('lecture_for_test');
    
    $this->conn->resetStats();
    
    $this->assertIsA($arr[0], 'CourseForTest');
    $this->assertEqual($arr[0]->getTitle(), $course1->getTitle());
    $lectures = $arr[0]->getLectures();
    $this->assertEqual(count($lectures), 3);
    $this->assertEqual($lectures[0]->getId(), $lecture3->getId());
    $this->assertEqual($lectures[0]->getTitle(), 'AAA');
    $this->assertEqual($lectures[1]->getId(), $lecture4->getId());
    $this->assertEqual($lectures[1]->getTitle(), 'BBB');
    $this->assertEqual($lectures[2]->getId(), $lecture1->getId());
    $this->assertEqual($lectures[2]->getTitle(), 'ZZZ');
    
    $this->assertIsA($arr[1], 'CourseForTest');
    $this->assertEqual($arr[1]->getTitle(), $course2->getTitle());
    $lectures = $arr[1]->getLectures();
    $this->assertEqual(count($lectures), 1);
    $this->assertEqual($lectures[0]->getId(), $lecture2->getId());
    $this->assertEqual($lectures[0]->getTitle(), 'CCC');
    
    $this->assertEqual($this->conn->countQueries(), 0);
  }
  
  function testFetch_Attach_RelatedHasMany_WithCriteriaForAttach()
  {
    $course1 = $this->creator->createCourse();
    $course2 = $this->creator->createCourse();
    
    $lecture1 = $this->creator->createLecture($course1, null, 'ZZZ');
    $lecture2 = $this->creator->createLecture($course2, null, 'CCC');
    $lecture3 = $this->creator->createLecture($course1, null, 'AAA');
    
    $query = lmbARQuery :: create('CourseForTest', array(), $this->conn);
    $arr = $query->eagerAttach('lectures', array('criteria' => lmbSQLCriteria :: equal('title', 'CCC')))->fetch()->getArray();
    
    $this->conn->resetStats();
    
    $this->assertIsA($arr[0], 'CourseForTest');
    $this->assertEqual($arr[0]->getTitle(), $course1->getTitle());
    $lectures = $arr[0]->getLectures();
    $this->assertEqual(count($lectures), 0);
    
    $this->assertIsA($arr[1], 'CourseForTest');
    $this->assertEqual($arr[1]->getTitle(), $course2->getTitle());
    $lectures = $arr[1]->getLectures();
    $this->assertEqual(count($lectures), 1);
    $this->assertEqual($lectures[0]->getId(), $lecture2->getId());
    $this->assertEqual($lectures[0]->getTitle(), 'CCC');
    
    $this->assertEqual($this->conn->countQueries(), 0);
    
    // let's change the first course and save it. The lectures should stay in database
    $arr[0]->setTitle('Changed');
    $arr[0]->save();
    
    $loaded_course = new CourseForTest($course1->getId());
    $lectures = $loaded_course->getLectures();
    $this->assertEqual(count($lectures), 2);
  }
  
  function testFetch_Attach_WithEmptyRS_ForRelatedHasMany()
  {
    $this->conn->resetStats();
    
    $query = lmbARQuery :: create('CourseForTest', array(), $this->conn);
    $arr = $query->eagerAttach('lectures')->fetch()->getArray();
    
    $this->assertEqual($this->conn->countQueries(), 1);
  }

  function testFetch_Attach_RelatedHasManyToMany()
  {
    $user1 = $this->creator->createUser();
    $user2 = $this->creator->createUser();

    $group1 = $this->creator->createGroup('AAA');
    $group2 = $this->creator->createGroup('BBB');
    $group3 = $this->creator->createGroup('ZZZ');
    
    $group1->setUsers(array($user1, $user2));
    $group2->setUsers(array($user2));
    $group3->setUsers(array($user1));
     
    $this->conn->resetStats();
    
    $query = lmbARQuery :: create('UserForTest', array(), $this->conn);
    $arr = $query->eagerAttach('groups', array('sort' => array('title' => 'DESC')))->fetch()->getArray();
    
    $this->assertEqual($this->conn->countQueries(), 2);
    
    //make sure we really eager fetching
    $this->db->delete('group_for_test');
    $this->db->delete('user_for_test2group_for_test');

    $this->conn->resetStats();
    
    $this->assertIsA($arr[0], 'UserForTest');
    $this->assertEqual($arr[0]->getFirstName(), $user1->getFirstName());
    $groups = $arr[0]->getGroups();
    $this->assertEqual(count($groups), 2);
    $this->assertEqual($groups[0]->getId(), $group3->getId());
    $this->assertEqual($groups[0]->getTitle(), 'ZZZ');
    $this->assertEqual($groups[1]->getId(), $group1->getId());
    $this->assertEqual($groups[1]->getTitle(), 'AAA');
    
    $this->assertIsA($arr[1], 'UserForTest');
    $this->assertEqual($arr[1]->getFirstName(), $user2->getFirstName());
    $groups = $arr[1]->getGroups();
    $this->assertEqual(count($groups), 2);
    $this->assertEqual($groups[0]->getId(), $group2->getId());
    $this->assertEqual($groups[0]->getTitle(), 'BBB');
    $this->assertEqual($groups[1]->getId(), $group1->getId());
    $this->assertEqual($groups[1]->getTitle(), 'AAA');
    
    $this->assertEqual($this->conn->countQueries(), 0);
  }

  function testFetch_NestedJoinProperty_In_Attach_ForHasMany()
  {
    $course1 = $this->creator->createCourse();
    $course2 = $this->creator->createCourse();
    
    $alt_course1 = $this->creator->createCourse();
    $alt_course2 = $this->creator->createCourse();
    
    $lecture1 = $this->creator->createLecture($course1, $alt_course2);
    $lecture2 = $this->creator->createLecture($course2, $alt_course1);
    $lecture3 = $this->creator->createLecture($course1, $alt_course2);
    $lecture4 = $this->creator->createLecture($course1, $alt_course1);
    
    $this->conn->resetStats();
    
    $query = lmbARQuery :: create('CourseForTest', array(), $this->conn);
    $query->where(lmbSQLCriteria :: in('id', array($course1->getId(), $course2->getId())));
    $rs = $query->eagerAttach('lectures', array('join' => 'alt_course'))->fetch();
    $arr = $rs->getArray();
    
    $this->assertEqual($this->conn->countQueries(), 2);
    
    //make sure we really eager fetching
    $this->db->delete('lecture_for_test');
    $this->db->delete('course_for_test');

    $this->conn->resetStats();
    
    $this->assertIsA($arr[0], 'CourseForTest');
    $this->assertEqual($arr[0]->getTitle(), $course1->getTitle());
    $lectures = $arr[0]->getLectures()->getArray();
    $this->assertEqual(count($lectures), 3);
    $this->assertEqual($lectures[0]->getId(), $lecture1->getId());
    $this->assertEqual($lectures[0]->getAltCourse()->getTitle(), $alt_course2->getTitle());
    $this->assertEqual($lectures[1]->getId(), $lecture3->getId());
    $this->assertEqual($lectures[1]->getAltCourse()->getTitle(), $alt_course2->getTitle());
    $this->assertEqual($lectures[2]->getId(), $lecture4->getId());
    $this->assertEqual($lectures[2]->getAltCourse()->getTitle(), $alt_course1->getTitle());
    
    
    $this->assertIsA($arr[1], 'CourseForTest');
    $this->assertEqual($arr[1]->getTitle(), $course2->getTitle());
    $lectures = $arr[1]->getLectures()->getArray();
    $this->assertEqual(count($lectures), 1);
    $this->assertEqual($lectures[0]->getId(), $lecture2->getId());
    $this->assertEqual($lectures[0]->getAltCourse()->getTitle(), $alt_course1->getTitle());
    
    $this->assertEqual($this->conn->countQueries(), 0);
  }  
  
  function testFetch_NestedAttachProperty_In_Join()
  {
    $course1 = $this->creator->createCourse();
    $course2 = $this->creator->createCourse();
    
    $alt_course1 = $this->creator->createCourse();
    $alt_course2 = $this->creator->createCourse();
    
    $lecture1 = $this->creator->createLecture($course1, $alt_course2);
    $lecture2 = $this->creator->createLecture($course2, $alt_course1);
    $lecture3 = $this->creator->createLecture($course1, $alt_course2);
    $lecture4 = $this->creator->createLecture($course1, $alt_course1);

    $lecture5 = $this->creator->createLecture($alt_course2);
    $lecture6 = $this->creator->createLecture($alt_course1);
    $lecture7 = $this->creator->createLecture($alt_course2);
    $lecture8 = $this->creator->createLecture($alt_course1);
    
    $this->conn->resetStats();

    $query = lmbARQuery :: create('LectureForTest', array(), $this->conn);
    $query->where(lmbSQLCriteria :: equal('course_id', $course1->getId()));
    $iterator = $query->eagerJoin('alt_course', array('attach' => 'lectures'))->fetch();
    $arr = $iterator->getArray();
    
    $this->assertEqual($this->conn->countQueries(), 2);
    
    //make sure we really eager fetching
    $this->db->delete('lecture_for_test');
    $this->db->delete('course_for_test');
    
    $this->conn->resetStats();
    
    $this->assertIsA($arr[0], 'LectureForTest');
    $this->assertEqual($arr[0]->getTitle(), $lecture1->getTitle());
    
    $this->assertEqual($arr[0]->getAltCourse()->getTitle(), $alt_course2->getTitle());
    $alt_course_lectures = $arr[0]->getAltCourse()->getLectures();
    $this->assertEqual($alt_course_lectures[0]->getId(), $lecture5->getId());
    $this->assertEqual($alt_course_lectures[1]->getId(), $lecture7->getId());
    
    $this->assertEqual($arr[1]->getId(), $lecture3->getId());
    $this->assertEqual($arr[1]->getAltCourse()->getTitle(), $alt_course2->getTitle());
    $alt_course_lectures = $arr[1]->getAltCourse()->getLectures();
    $this->assertEqual($alt_course_lectures[0]->getId(), $lecture5->getId());
    $this->assertEqual($alt_course_lectures[1]->getId(), $lecture7->getId());
    
    $this->assertEqual($arr[2]->getId(), $lecture4->getId());
    $this->assertEqual($arr[2]->getAltCourse()->getTitle(), $alt_course1->getTitle());
    $alt_course_lectures = $arr[2]->getAltCourse()->getLectures();
    $this->assertEqual($alt_course_lectures[0]->getId(), $lecture6->getId());
    $this->assertEqual($alt_course_lectures[1]->getId(), $lecture8->getId());
    
    $this->assertEqual($this->conn->countQueries(), 0);
  }  

  function testFetchNested_AttachProperty_In_JoinProperty_In_Attach()
  {
    $course1 = $this->creator->createCourse();
    $course2 = $this->creator->createCourse();
    
    $alt_course1 = $this->creator->createCourse();
    $alt_course2 = $this->creator->createCourse();
    
    $lecture1 = $this->creator->createLecture($course1, $alt_course2);
    $lecture2 = $this->creator->createLecture($course2, $alt_course1);
    $lecture3 = $this->creator->createLecture($course1, $alt_course2);
    $lecture4 = $this->creator->createLecture($course1, $alt_course1);
    
    $lecture5 = $this->creator->createLecture($alt_course2);
    $lecture6 = $this->creator->createLecture($alt_course1);
    $lecture7 = $this->creator->createLecture($alt_course2);
    $lecture8 = $this->creator->createLecture($alt_course1);
    
    $this->conn->resetStats();
    
    $query = lmbARQuery :: create('CourseForTest', array(), $this->conn);
    $query->where(lmbSQLCriteria :: in('id', array($course1->getId(), $course2->getId())));
    $arr = $query->eagerAttach('lectures', array('join' => array('alt_course' => array('attach' => 'lectures'))))->fetch()->getArray();

    $this->assertEqual($this->conn->countQueries(), 3);
    
    //make sure we really eager fetching
    $this->db->delete('lecture_for_test');
    $this->db->delete('course_for_test');

    $this->conn->resetStats();
    
    $this->assertIsA($arr[0], 'CourseForTest');
    $this->assertEqual($arr[0]->getTitle(), $course1->getTitle());
    $lectures = $arr[0]->getLectures()->getArray();
    $this->assertEqual(count($lectures), 3);
    $this->assertEqual($lectures[0]->getId(), $lecture1->getId());
    $this->assertEqual($lectures[0]->getAltCourse()->getTitle(), $alt_course2->getTitle());
    $alt_course_lectures = $lectures[0]->getAltCourse()->getLectures();
    $this->assertEqual($alt_course_lectures[0]->getId(), $lecture5->getId());
    $this->assertEqual($alt_course_lectures[1]->getId(), $lecture7->getId());
    
    $this->assertEqual($lectures[1]->getId(), $lecture3->getId());
    $this->assertEqual($lectures[1]->getAltCourse()->getTitle(), $alt_course2->getTitle());
    $alt_course_lectures = $lectures[1]->getAltCourse()->getLectures();
    $this->assertEqual($alt_course_lectures[0]->getId(), $lecture5->getId());
    $this->assertEqual($alt_course_lectures[1]->getId(), $lecture7->getId());
    
    $this->assertEqual($lectures[2]->getId(), $lecture4->getId());
    $this->assertEqual($lectures[2]->getAltCourse()->getTitle(), $alt_course1->getTitle());
    $alt_course_lectures = $lectures[2]->getAltCourse()->getLectures();
    $this->assertEqual($alt_course_lectures[0]->getId(), $lecture6->getId());
    $this->assertEqual($alt_course_lectures[1]->getId(), $lecture8->getId());

    $this->assertIsA($arr[1], 'CourseForTest');
    $this->assertEqual($arr[1]->getTitle(), $course2->getTitle());
    $lectures = $arr[1]->getLectures()->getArray();
    $this->assertEqual(count($lectures), 1);
    $this->assertEqual($lectures[0]->getId(), $lecture2->getId());
    $this->assertEqual($lectures[0]->getAltCourse()->getTitle(), $alt_course1->getTitle());
    
    $alt_course_lectures = $lectures[0]->getAltCourse()->getLectures();
    $this->assertEqual($alt_course_lectures[0]->getId(), $lecture6->getId());
    $this->assertEqual($alt_course_lectures[1]->getId(), $lecture8->getId());
    
    $this->assertEqual($this->conn->countQueries(), 0);
  }  
  
  function testFetch_NestedJoinProperty_In_Join()
  {
    $program1 = $this->creator->createProgram();
    $program2 = $this->creator->createProgram();
    $course1 = $this->creator->createCourse($program1);
    $course2 = $this->creator->createCourse($program2);
    $lecture1 = $this->creator->createLecture($course1);
    $lecture2 = $this->creator->createLecture($course2);
    
    $this->conn->resetStats();
    
    $query = lmbARQuery :: create('LectureForTest', array(), $this->conn);
    $iterator = $query->eagerJoin('course', array('join' => 'program'))->fetch();
    $arr = $iterator->getArray();
    
    $this->assertEqual($this->conn->countQueries(), 1);
    
    //make sure we really eager fetching
    $this->db->delete('lecture_for_test');
    $this->db->delete('course_for_test');
    $this->db->delete('program_for_test');

    $this->conn->resetStats();
    
    $this->assertIsA($arr[0], 'LectureForTest');
    $this->assertEqual($arr[0]->getTitle(), $lecture1->getTitle());
    $this->assertEqual($arr[1]->getTitle(), $lecture2->getTitle());
    
    $this->assertEqual($arr[0]->getCourse()->getTitle(), $course1->getTitle());
    $this->assertEqual($arr[1]->getCourse()->getTitle(), $course2->getTitle());

    $this->assertEqual($arr[0]->getCourse()->getProgram()->getTitle(), $program1->getTitle());
    $this->assertEqual($arr[1]->getCourse()->getProgram()->getTitle(), $program2->getTitle());
    
    $this->assertEqual($this->conn->countQueries(), 0);
  }

  function testFetch_NestedAttachProperty_In_Attach()
  {
    $program1 = $this->creator->createProgram();
    $program2 = $this->creator->createProgram();
    $course1 = $this->creator->createCourse($program1);
    $course2 = $this->creator->createCourse($program2);
    $course3 = $this->creator->createCourse($program1);
    $course4 = $this->creator->createCourse($program2);
    $lecture1 = $this->creator->createLecture($course1);
    $lecture2 = $this->creator->createLecture($course2);
    $lecture3 = $this->creator->createLecture($course3);
    $lecture4 = $this->creator->createLecture($course4);
    $lecture5 = $this->creator->createLecture($course1);
    $lecture6 = $this->creator->createLecture($course2);
    $lecture7 = $this->creator->createLecture($course3);
    $lecture8 = $this->creator->createLecture($course4);
    
    $this->conn->resetStats();
    
    $query = lmbARQuery :: create('ProgramForTest', array(), $this->conn);
    $iterator = $query->eagerAttach('courses', array('attach' => 'lectures'))->fetch();
    
    $arr = $iterator->getArray();

    $this->assertEqual($this->conn->countQueries(), 3);
    
    //make sure we really eager fetching
    $this->db->delete('lecture_for_test');
    $this->db->delete('course_for_test');
    $this->db->delete('program_for_test');

    $this->conn->resetStats();
    
    $this->assertIsA($arr[0], 'ProgramForTest');
    $this->assertEqual($arr[0]->getTitle(), $program1->getTitle());
    
    $courses = $arr[0]->getCourses()->getArray();
    
    $this->assertEqual($courses[0]->getTitle(), $course1->getTitle());
    $lectures = $courses[0]->getLectures()->getArray();
    $this->assertEqual($lectures[0]->getTitle(), $lecture1->getTitle());
    $this->assertEqual($lectures[1]->getTitle(), $lecture5->getTitle());

    $this->assertEqual($courses[1]->getTitle(), $course3->getTitle());
    $lectures = $courses[1]->getLectures()->getArray();
    $this->assertEqual($lectures[0]->getTitle(), $lecture3->getTitle());
    $this->assertEqual($lectures[1]->getTitle(), $lecture7->getTitle());

    $this->assertEqual($arr[1]->getTitle(), $program2->getTitle());
    
    $courses = $arr[1]->getCourses()->getArray();
    
    $this->assertEqual($courses[0]->getTitle(), $course2->getTitle());
    $lectures = $courses[0]->getLectures()->getArray();
    $this->assertEqual($lectures[0]->getTitle(), $lecture2->getTitle());
    $this->assertEqual($lectures[1]->getTitle(), $lecture6->getTitle());

    $this->assertEqual($courses[1]->getTitle(), $course4->getTitle());
    $lectures = $courses[1]->getLectures()->getArray();
    $this->assertEqual($lectures[0]->getTitle(), $lecture4->getTitle());
    $this->assertEqual($lectures[1]->getTitle(), $lecture8->getTitle());
    
    $this->assertEqual($this->conn->countQueries(), 0);
  }    

  function testFetch_JoinWorkdsOkIfJoinedObjectIsNotSet()
  {
    $program = $this->creator->createProgram();
    $course1 = $this->creator->createCourse($program);
    $course2 = $this->creator->createCourse();
    
    $query = lmbARQuery :: create('CourseForTest', array(), $this->conn);
    $arr = $query->eagerJoin('program')->fetch()->getArray();
    
    $this->assertEqual($arr[0]->getProgram()->getTitle(), $program->getTitle());
    $this->assertNull($arr[1]->getProgram());
  }  

  function testFetch_AttachWithNothingToAttach()
  {
    $program1 = $this->creator->createProgram();
    $program2 = $this->creator->createProgram();
    
    $this->conn->resetStats();
    
    $query = lmbARQuery :: create('ProgramForTest', array(), $this->conn);
    $arr = $query->eagerAttach('courses')->fetch()->getArray();

    $this->assertEqual($this->conn->countQueries(), 2);

    $this->conn->resetStats();
    
    $this->assertEqual($arr[0]->getTitle(), $program1->getTitle());
    $this->assertEqual($arr[1]->getTitle(), $program2->getTitle());
    $this->assertEqual($arr[0]->getCourses()->count(), 0);
    $this->assertEqual($arr[1]->getCourses()->count(), 0);

    $this->assertEqual($this->conn->countQueries(), 0);
  }  

  function testFetch_JoinWithWrongRelationType()
  {
    $program1 = $this->creator->createProgram();
    $program2 = $this->creator->createProgram();
    
    $query = lmbARQuery :: create('ProgramForTest', array(), $this->conn);
    $query->eagerJoin('courses');
    try
    {
      $it = $query->fetch();
      $this->assertTrue(false);
    }
    catch(lmbARException $e)
    {
      $this->assertTrue(true);
    }
  }  
  
  function testFetch_AttachManyBelongsToRelationWithNothingToAttach()
  {
    $course1 = $this->creator->createCourse();
    $course2 = $this->creator->createCourse();
    
    $this->conn->resetStats();
    
    $query = lmbARQuery :: create('CourseForTest', array(), $this->conn);
    $arr = $query->eagerAttach('program')->fetch()->getArray();

    $this->assertEqual($this->conn->countQueries(), 2);    

    $this->conn->resetStats();
    $this->assertEqual($arr[0]->getTitle(), $course1->getTitle());
    $this->assertEqual($arr[1]->getTitle(), $course2->getTitle());

    $this->assertNull($arr[0]->getProgram(), 0);
    $this->assertNull($arr[1]->getProgram(), 0);
    
    $this->assertEqual($this->conn->countQueries(), 0);    
  }
  
  function testGroup()
  {
  	$course1 = $this->creator->createCourse();
  	$lecture1 = $this->creator->createLecture($course1);
  	$lecture2 = $this->creator->createLecture($course1);
  	
  	$course2 = $this->creator->createCourse();
  	$lecture3 = $this->creator->createLecture($course2);
  	$lecture4 = $this->creator->createLecture($course2);
  	
  	$query = lmbARQuery :: create('LectureForTest', array('group' => 'course.id'), $this->conn);
  	$rs = $query->eagerJoin('course')->fetch();
  	
  	$this->assertEqual($rs->count(), 2);
  	
  	$arr = $rs->getArray();
  	$this->assertEqual(count($arr), 2);
  	$this->assertEqual($arr[0]->getTitle(), $lecture1->getTitle());
  	$this->assertEqual($arr[1]->getTitle(), $lecture3->getTitle());
  }
}


