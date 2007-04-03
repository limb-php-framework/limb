<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbDatamapTest.class.php 4992 2007-02-08 15:35:40Z pachanga $
 * @package    datasource
 */
lmb_require('limb/classkit/src/lmbHandle.class.php');
lmb_require('limb/datasource/src/lmbDatamap.class.php');
lmb_require('limb/datasource/src/lmbDataspace.class.php');

class DatamapTestStub
{
  var $some_foo_property = 'default';

  function getSomeFooProperty()
  {
    return $this->some_foo_property;
  }

  function setSomeFooProperty($value)
  {
    $this->some_foo_property = $value;
  }
}

class lmbDatamapTest extends UnitTestCase
{
  function testMapNothing()
  {
    $datamap = new lmbDatamap();

    $source = array('foo' => 1, 'bar' => 2);
    $dest = array();

    $this->assertEqual($dest, array());
  }

  function testAddMapping()
  {
    $datamap = new lmbDatamap();

    $source = array('foo' => 1, 'bar' => 2);
    $dest = array();

    $datamap->addMapping('foo', 'wow');
    $datamap->addMapping('bar', 'yo');

    $datamap->map($source, $dest);

    $this->assertEqual($dest, array('wow' => 1, 'yo' => 2));
  }

  function testAddOneWayMapping()
  {
    $datamap = new lmbDatamap();

    $source = array('foo' => 1, 'bar' => 2);
    $dest = array();

    $datamap->addMapping('foo');
    $datamap->addMapping('bar');

    $datamap->map($source, $dest);

    $this->assertEqual($dest, array('foo' => 1, 'bar' => 2));
  }

  function testBindDestinationToSource()
  {
    $datamap = new lmbDatamap();

    $source = array('foo' => 1, 'bar' => 2);
    $dest = array();

    $datamap->addMapping('foo', 'wow');
    $datamap->bindDestinationToSource('wow', 'bar');

    $datamap->map($source, $dest);

    $this->assertEqual($dest, array('wow' => 2));
  }

  function testReverseMap()
  {
    $datamap = new lmbDatamap();

    $source = array('wow' => 1, 'yo' => 2);
    $dest = array();

    $datamap->addMapping('foo', 'wow');
    $datamap->addMapping('bar', 'yo');

    $datamap->reverseMap($source, $dest);

    $this->assertEqual($dest, array('foo' => 1, 'bar' => 2));
  }

  function testMapObjectsUsingGenericGetSet()
  {
    $datamap = new lmbDatamap();

    $source = new lmbDataspace(array('foo' => 1, 'bar' => 2, 'zoo' => 3));
    $dest = new lmbDataspace();

    $datamap->addMapping('foo', 'wow');
    $datamap->addMapping('bar', 'yo');

    $datamap->map($source, $dest);

    $this->assertEqual($dest->export(), array('wow' => 1, 'yo' => 2));
  }

  function testMapObjectsUsingFineGrainedGetSet()
  {
    $datamap = new lmbDatamap();

    $source = new DatamapTestStub();
    $source->some_foo_property = 'a';

    $dest = new DatamapTestStub();
    $dest->some_foo_property = 'b';

    $datamap->addMapping('some_foo_property');

    $datamap->map($source, $dest);

    $this->assertEqual($dest->some_foo_property, 'a');
  }

  function testSourceHandleIsResolvedBeforeMapping()
  {
    $datamap = new lmbDatamap();

    $source = new DatamapTestStub();
    $source->some_foo_property = 'a';

    $dest = new lmbHandle('DatamapTestStub');

    $datamap->addMapping('some_foo_property');

    $datamap->map($source, $dest);

    $this->assertEqual($dest->some_foo_property, 'a');
  }

  function testDestHandleIsResolvedBeforeMapping()
  {
    $datamap = new lmbDatamap();

    $source = new lmbHandle('DatamapTestStub');

    $dest = new DatamapTestStub();
    $dest->some_foo_property = 'a';

    $datamap->addMapping('some_foo_property');

    $datamap->map($source, $dest);

    $this->assertEqual($dest->some_foo_property, 'default');
  }

  function testException()
  {
    $datamap = new lmbDatamap();

    $source = new DatamapTestStub();
    $dest = new DatamapTestStub();

    $datamap->addMapping('junk');

    try
    {
      $datamap->map($source, $dest);
      $this->assertTrue(false);
    }
    catch(lmbException $e){}
  }
}


?>
