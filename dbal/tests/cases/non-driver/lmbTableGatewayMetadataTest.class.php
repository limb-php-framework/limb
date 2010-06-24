<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/dbal/src/criteria/lmbSQLFieldCriteria.class.php');
lmb_require('limb/dbal/src/lmbTableGateway.class.php');
lmb_require('limb/dbal/src/drivers/lmbDbCachedInfo.class.php');

class lmbTableGatewayMetadataTest extends UnitTestCase
{
  var $conn = null;

  function setUp()
  {
    $toolkit = lmbToolkit :: save();    
    $this->conn = $toolkit->getDefaultDbConnection();
  }

  function tearDown()
  {
    lmbToolkit :: restore();
  }

  function testFillMetaInfoFromDB()
  {
    $table = new lmbTableGateway('all_types_test', $this->conn);

    $expected = array('field_int' => 'field_int',
                      'field_varchar' => 'field_varchar',
                      'field_char' => 'field_char',
                      'field_date' => 'field_date',
                      'field_datetime' => 'field_datetime',
                      'field_time' => 'field_time',
                      'field_text' => 'field_text',
                      'field_smallint' => 'field_smallint',
                      'field_bigint' => 'field_bigint',
                      'field_blob' => 'field_blob',
                      'field_float' => 'field_float',
                      'field_decimal' => 'field_decimal',
                      'field_tinyint' => 'field_tinyint');

    $this->assertEqual($table->getColumnNames(), $expected);
  }
}

