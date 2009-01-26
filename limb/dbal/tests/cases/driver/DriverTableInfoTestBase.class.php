<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
require_once(dirname(__FILE__) . '/DriverMetaTestBase.class.php');

abstract class DriverTableInfoTestBase extends DriverMetaTestBase
{
  /**
   * @var lmbDatabaseInfo
   */
  var $database_info;
  /**
   * @var lmbDbTableInfo
   */
  var $table_info;

  function setUp()
  {
    $this->database_info = $this->connection->getDatabaseInfo();
    $this->table_info = $this->database_info->getTable('founding_fathers');
  }

  function tearDown()
  {
    unset($this->table);
    parent::tearDown();
  }

  function testGetDatabase()
  {
    $db = $this->table_info->getDatabase();
    $this->assertIsA($db, 'lmbDbInfo');
  }

  function testGetName()
  {
    $this->assertEqual($this->table_info->getName(), 'founding_fathers');
  }

  function testHasColumn()
  {
    $this->assertTrue($this->table_info->hasColumn('id'));
    $this->assertTrue($this->table_info->hasColumn('first'));
    $this->assertTrue($this->table_info->hasColumn('last'));
    $this->assertFalse($this->table_info->hasColumn('tiabaltu'));
  }

  function testGetColumn()
  {
    $column = $this->table_info->getColumn('last');
    $this->assertIsA($column, 'lmbDbColumnInfo');
  }

  function testGetColumnList()
  {
    $this->assertEqual($this->table_info->getColumnList(),
          array('id' => 'id', 'first' => 'first', 'last' => 'last'));
  }
  
  function testGetColumns()
  {
    $columns = $this->table_info->getColumns();
    if($this->assertTrue(isset($columns['id'])))
      if($this->assertIsA($columns['id'], 'lmbDbColumnInfo'))
        $this->assertEqual($columns['id']->getName(), 'id');    
  }


  function _isIndexImplemented()
  {
    $db_types_with_support = array('mysql', 'mysqli');

    $current_type = lmbToolkit::instance()->getDefaultDbConnection()->getType();

    return in_array($current_type, $db_types_with_support);
  }

  function testHasIndex()
  {
    if(!$this->_isIndexImplemented())
      return;

    $table = $this->database_info->getTable('indexes');

    $this->assertTrue($table->hasIndex('primary_column'));
    $this->assertFalse($table->hasIndex('tiabaltu_index'));
  }

  function testGetIndex()
  {
    if(!$this->_isIndexImplemented())
      return;

    $table = $this->database_info->getTable('indexes');

    $index = $table->getIndex('primary_column');
    $this->assertIsA($index, 'lmbDbIndexInfo');
  }

  function testGetIndexList()
  {
    if(!$this->_isIndexImplemented())
      return;

    $table = $this->database_info->getTable('indexes');

    $this->assertEqual($table->getIndexList(),
      array('primary_column' => 'primary_column',
            'unique_column_named_index' => 'unique_column_named_index',
            'common_column' => 'common_column'));
  }

  function testGetIndexForColumn()
  {
    if(!$this->_isIndexImplemented())
      return;

    $table = $this->database_info->getTable('indexes');

    $index = $table->getIndexForColumn('primary_column');
    if($this->assertIsA($index, 'lmbDbIndexInfo'))
      $this->assertIdentical('primary_column', $index->getName());

    $index = $table->getIndexForColumn('unique_column');
    if($this->assertIsA($index, 'lmbDbIndexInfo'))
      $this->assertIdentical('unique_column_named_index', $index->getName());

  }
}


