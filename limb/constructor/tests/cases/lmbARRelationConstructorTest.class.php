<?php
lmb_require('limb/constructor/src/lmbARRelationConstructor.class.php');
lmb_require('limb/constructor/src/lmbARRelationAggrementsResolver.class.php');
lmb_require('limb/dbal/src/drivers/lmbDbTableInfo.class.php');

class lmbARRelationConstructorTest extends UnitTestCase
{
  /**
   * @var lmbARRelationConstructor
   */
  protected $constructor;

  function _getTablesList()
  {
    $conn = lmbToolkit::instance()->getDefaultDbConnection();
    return $conn->getDatabaseInfo()->getTableList();
  }

  function setUp()
  {
    $conn = lmbToolkit::instance()->getDefaultDbConnection();
    $this->constructor = new lmbARRelationConstructor(
      $conn->getDatabaseInfo(),
      new lmbARRelationAggrementsResolver()
    );
  }

  function testGetColumnsFromAllTablesByName()
  {
    $result = $this->constructor->getColumnsFromAllTablesByName('course_id');
    if($this->assertEqual(count($result), 1))
      if($this->assertIsA($result[0], 'lmbDbColumnInfo'))
      {
        $this->assertEqual($result[0]->getName(), 'course_id');
        $this->assertEqual($result[0]->getTable()->getName(), 'lecture');
      }
  }

  function testGetRelationsHasOneFor_Positive()
  {
    $must_be = array(
      'social_security' => array(
        'field' => 'social_security_id',
        'class' => 'SocialSecurity',
        'cascade_delete' => false,
        'can_be_null' => true)
    );

    $result = $this->constructor->getRelationsHasOneFor('person');
    $this->assertIdentical($result, $must_be);
  }

  function testGetRelationsHasOneFor_Negative()
  {
    foreach ($this->_getTablesList() as $table)
    {
      if($table == 'person') continue;
      $result = $this->constructor->getRelationsHasOneFor($table);
      $this->assertIdentical($result, array());
    }
  }

  function testGetRelationsBelongsFor_Positive()
  {
    $must_be = array(
      'person' => array(
        'field' => 'social_security_id',
        'class' => 'Person')
    );

    $result = $this->constructor->getRelationsBelongsFor('social_security');
    $this->assertIdentical($result, $must_be);
  }

  function testGetRelationsBelongsFor_Negative()
  {
    foreach ($this->_getTablesList() as $table)
    {
      if($table == 'social_security') continue;
      $result = $this->constructor->getRelationsBelongsFor($table);
      $this->assertIdentical($result, array());
    }
  }

  function testGetRelationsHasManyFor_Positive()
  {
    $must_be = array(
      'lectures' => array(
        'field' => 'course_id',
        'class' => 'Lecture',
        'nullify' => true
    ));

    $result = $this->constructor->getRelationsHasManyFor('course');
    $this->assertIdentical($result, $must_be);
  }

  function testGetRelationsHasManyFor_Negative()
  {
    foreach ($this->_getTablesList() as $table)
    {
      if($table == 'course') continue;
      $result = $this->constructor->getRelationsHasManyFor($table);
      $this->assertIdentical($result, array());
      if(count($result))
        var_dump($table);
    }
  }

  function testGetRelationsManyBelongsFor_Positive()
  {
    $must_be = array(
      'course' => array(
        'field' => 'course_id',
        'class' => 'Course',
        'can_be_null' => true
    ));

    $result = $this->constructor->getRelationsManyBelongsFor('lecture');
    $this->assertIdentical($result, $must_be);
  }

  function testGetRelationsManyBelongsFor_Negative()
  {
    foreach ($this->_getTablesList() as $table)
    {
      if($table == 'lecture') continue;
      $result = $this->constructor->getRelationsManyBelongsFor($table);
      $this->assertIdentical($result, array());
    }
  }

  function testGetRelationsManyToManyFor_Positive()
  {
    $must_be_user = array(
     'users' => array(
        'field' => 'group_id',
        'foreign_field' => 'user_id',
        'table' => 'user2group',
        'class' => 'User')
    );

    $must_be_group = array(
     'groups' => array(
        'field' => 'user_id',
        'foreign_field' => 'group_id',
        'table' => 'user2group',
        'class' => 'Group')
    );

    $result = $this->constructor->getRelationsManyToManyFor('group');
    $this->assertIdentical($result, $must_be_user);

    $result = $this->constructor->getRelationsManyToManyFor('user');
    $this->assertIdentical($result, $must_be_group);
  }

  function testGetRelationsManyToManyFor_Negative()
  {
    foreach ($this->_getTablesList() as $table)
    {
      if($table == 'group' || $table == 'user') continue;
      $result = $this->constructor->getRelationsManyToManyFor($table);
      $this->assertIdentical($result, array());
    }
  }
}
