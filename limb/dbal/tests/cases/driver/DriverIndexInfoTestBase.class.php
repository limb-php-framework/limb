<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
require_once(dirname(__FILE__) . '/DriverMetaTestBase.class.php');

abstract class DriverIndexInfoTestBase extends DriverMetaTestBase
{
  /**
   * @var lmbDbTableInfo
   */
  var $table;

  protected $_index_names = array(
    'primary' => 'primary_column',
    'unique' => 'unique_column_named_index',
    'common' => 'common_column',
  );

  function setUp()
  {
    $dbinfo = $this->connection->getDatabaseInfo();
    $this->table = $dbinfo->getTable('indexes');
  }

  function testFetchingIndexes()
  {
    $indexes = $this->table->getIndexList();

    $this->assertEqual(3, count($indexes));

    $this->assertTrue($this->table->hasIndex($this->_index_names['primary']));
    $primary_index = $this->table->getIndex($this->_index_names['primary']);
    $this->assertTrue($primary_index->isPrimary());
    $this->assertEqual('primary_column', $primary_index->column_name);

    $this->assertTrue($this->table->hasIndex($this->_index_names['unique']));
    $unique_index = $this->table->getIndex($this->_index_names['unique']);
    $this->assertTrue($unique_index->isUnique());
    $this->assertEqual('unique_column', $unique_index->column_name);

    $this->assertTrue($this->table->hasIndex($this->_index_names['common']));
    $common_index = $this->table->getIndex($this->_index_names['common']);
    $this->assertTrue($common_index->isCommon());
    $this->assertEqual('common_column', $common_index->column_name);
  }

  function tearDown()
  {
    unset($this->table);
    parent::tearDown();
  }
}



