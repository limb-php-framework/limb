<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/cms/src/model/lmbCmsClassName.class.php');
lmb_require('limb/dbal/src/lmbSimpleDb.class.php');

class lmbCmsClassNameTesingObject{}

class lmbCmsClassNameTest extends UnitTestCase
{
  protected $db;

  function setUp()
  {
    $toolkit = lmbToolkit :: instance();
    $this->db = new lmbSimpleDb($toolkit->getDefaultDbConnection());

    $this->_cleanUp();
  }

  function tearDown()
  {
    $this->_cleanUp();
  }

  function _cleanUp()
  {
    lmbActiveRecord :: delete('lmbCmsClassName');
  }

  function testGenerateIdFirstTimeCreateNewRecord()
  {
    $object = new lmbCmsClassNameTesingObject();
    $id = lmbCmsClassName :: generateIdFor($object);

    $class_name = lmbActiveRecord :: findById('lmbCmsClassName', $id);
    $this->assertEqual($class_name->title, 'lmbCmsClassNameTesingObject');
  }

  function testReturnExistingRecordId()
  {
    $object = new lmbCmsClassNameTesingObject();
    $id1 = lmbCmsClassName :: generateIdFor($object);
    $this->assertNotNull($id1);

    $id2 = lmbCmsClassName :: generateIdFor($object);
    $this->assertEqual($id1, $id2);
  }

  function testGenerateIdForNonObject()
  {
    $id = lmbCmsClassName :: generateIdFor('lmbCmsClassNameTesingObject');

    $class_name = lmbActiveRecord :: findById('lmbCmsClassName', $id);
    $this->assertEqual($class_name->title, 'lmbCmsClassNameTesingObject');
  }

}


