<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

class TestOneTableObjectWithRelationsByMethods extends lmbActiveRecord
{
  protected $_db_table_name = 'test_one_table_object';

  public $relations = array();

  function _defineRelations()
  {
    parent :: _defineRelations();

    $this->_hasOne('has_one_relation', $has_one = array('field' => 'child_id',
                                                        'class' => 'ChildClass'));
    $this->relations['has_one_relation'] = $has_one;


    $this->_hasOne('other_has_one_relation', $other_has_one = array('field' => 'other_child_id',
                                                                    'class' => 'OtherChildClass'));
    $this->relations['other_has_one_relation'] = $other_has_one;


    $this->_hasMany('has_many_relation', $has_many = array('field' => 'parent_id',
                                                           'class' => 'ManyChildClass'));
    $this->relations['has_many_relation'] = $has_many;


    $this->_hasMany('other_has_many_relation', $other_has_many = array('field' => 'other_parent_id',
                                                                       'class' => 'OtherManyChildClass'));
    $this->relations['other_has_many_relation'] = $other_has_many;


    $this->_hasManyToMany('has_many_to_many_relation',  $many_to_many = array('field' => 'my_id',
                                                                              'foreign_field' => 'important_id',
                                                                              'class' => 'ImportantClass',
                                                                              'table_name' => 'me2importand_class'));
    $this->relations['has_many_to_many_relation'] = $many_to_many;


    $this->_hasManyToMany('other_has_many_to_many_relation',  $other_many_to_many = array('field' => 'my_id',
                                                                                          'foreign_field' => 'other_important_id',
                                                                                          'class' => 'OtherImportantClass',
                                                                                          'table_name' => 'me2other_importand_class'));
    $this->relations['other_has_many_to_many_relation'] = $other_many_to_many;


    $this->_belongsTo('belongs_to_relation', $belongs_to = array('field' => 'my_id',
                                                                 'class' => 'ParentClass'));
    $this->relations['belongs_to_relation'] = $belongs_to;


    $this->_belongsTo('other_belongs_to_relation', $other_belongs_to = array('field' => 'my_id',
                                                                             'class' => 'OtherParentClass'));
    $this->relations['other_belongs_to_relation'] = $other_belongs_to;


    $this->_manyBelongsTo('many_belongs_to_relation', $many_belongs_to = array('field' => 'parent_id',
                                                                               'class' => 'ParentClass'));
    $this->relations['many_belongs_to_relation'] = $many_belongs_to;


    $this->_manyBelongsTo('other_many_belongs_to_relation', $other_many_belongs_to =  array('field' => 'parent_id',
                                                                                            'class' => 'OtherParentClass'));
    $this->relations['other_many_belongs_to_relation'] = $other_many_belongs_to;

    $this->_composedOf('value_object', $value_object = array('field' => 'date_start',
                                                             'class' => 'lmbDateTime',
                                                             'getter' => 'getStamp'));

    $this->relations['value_object'] = $value_object;


    $this->_composedOf('other_value_object', $other_value_object = array('field' => 'date_end',
                                                                         'class' => 'lmbDateTime',
                                                                         'getter' => 'getStamp'));
    $this->relations['other_value_object'] = $other_value_object;
  }
}

class lmbARRelationsDefinitionMethodsTest extends UnitTestCase
{
  protected $object;

  function setUp()
  {
    $this->object = new TestOneTableObjectWithRelationsByMethods();
    $this->relations = $this->object->relations;
  }

  function testHasOne()
  {
    $this->assertEqual($this->object->getRelationInfo('has_one_relation'), $this->relations['has_one_relation']);
    $this->assertEqual($this->object->getRelationInfo('other_has_one_relation'), $this->relations['other_has_one_relation']);
  }

  function testHasMany()
  {
    $this->assertEqual($this->object->getRelationInfo('has_many_relation'), $this->relations['has_many_relation']);
    $this->assertEqual($this->object->getRelationInfo('other_has_many_relation'), $this->relations['other_has_many_relation']);
  }

  function testHasManyToMany()
  {
    $this->assertEqual($this->object->getRelationInfo('has_many_to_many_relation'), $this->relations['has_many_to_many_relation']);
    $this->assertEqual($this->object->getRelationInfo('other_has_many_to_many_relation'), $this->relations['other_has_many_to_many_relation']);
  }

  function testBelongsTo()
  {
    $this->assertEqual($this->object->getRelationInfo('belongs_to_relation'), $this->relations['belongs_to_relation']);
    $this->assertEqual($this->object->getRelationInfo('other_belongs_to_relation'), $this->relations['other_belongs_to_relation']);
  }

  function testManyBelongsTo()
  {
    $this->assertEqual($this->object->getRelationInfo('many_belongs_to_relation'), $this->relations['many_belongs_to_relation']);
    $this->assertEqual($this->object->getRelationInfo('other_many_belongs_to_relation'), $this->relations['other_many_belongs_to_relation']);
  }

  function testComposedOf()
  {
    $this->assertEqual($this->object->getRelationInfo('value_object'), $this->relations['value_object']);
    $this->assertEqual($this->object->getRelationInfo('other_value_object'), $this->relations['other_value_object']);
  }
}


