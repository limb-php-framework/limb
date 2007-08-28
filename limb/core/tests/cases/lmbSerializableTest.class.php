<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/core/src/lmbSerializable.class.php');
lmb_require(dirname(__FILE__) . '/serializable_stubs.inc.php');

class lmbSerializableTest extends UnitTestCase
{
  function testCheckClassPaths()
  {
    $stub = new SerializableTestStub();
    $stub->again = new SerializableTestStub();

    $container = new lmbSerializable($stub);

    $this->assertEqual($container->getClassPaths(), array());

    $serialized = serialize($container);

    $this->assertEqual($container->getClassPaths(), array($this->_getClassPath('SerializableTestStub'),
                                                          $this->_getClassPath('SerializableTestChildStub')));
  }

  function testSerializeUnserialize()
  {
    $stub = new SerializableTestStub();
    $container = new lmbSerializable($stub);

    $file = $this->_writeToFile(serialize($container));
    $this->_phpSerializedObjectCall($file, '->identify()', $stub);
    $this->_phpSerializedObjectCall($file, '->getChild()->identify()', $stub->getChild()->identify());
    unlink($file);
  }

  function testSerializeUnserializeWithoutSubjectLazyLoading()
  {
    $stub = new SerializableTestStub();
    $container1 = new lmbSerializable($stub);

    $file1 = $this->_writeToFile(serialize($container1));
    $container2 = unserialize(file_get_contents($file1));

    $file2 = $this->_writeToFile(serialize($container2));
    $this->_phpSerializedObjectCall($file2, '->identify()', $stub);
    $this->_phpSerializedObjectCall($file2, '->getChild()->identify()', $stub->getChild()->identify());

    unlink($file1);
    unlink($file2);
  }

  function testExtractSerializedClasses()
  {
    $stub = new SerializableTestChildStub();
    $serialized = serialize($stub);
    $this->assertEqual(lmbSerializable :: extractSerializedClasses($serialized), array('SerializableTestChildStub'));
  }

  function _writeToFile($serialized)
  {
    $tmp_serialized_file = LIMB_VAR_DIR . '/serialized.' . mt_rand();
    file_put_contents($tmp_serialized_file, $serialized);
    return $tmp_serialized_file;
  }

  function _phpSerializedObjectCall($file, $call)
  {
    $class_path = $this->_getClassPath('lmbSerializable');

    $cmd = "php -r \"require_once('$class_path');" .
           "echo unserialize(file_get_contents('$file'))->getSubject()$call;\"";

    exec($cmd, $ret, $out);
    return $out;
  }

  function _getClassPath($class)
  {
    $ref = new ReflectionClass($class);
    return $ref->getFileName();
  }
}


