<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
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

    $this->assertEqual($this->_phpSerializedObjectCall($file, '->identify()'), $stub->identify());
    $this->assertEqual($this->_phpSerializedObjectCall($file, '->getChild()->identify()'), $stub->getChild()->identify());
    unlink($file);
  }

  function testExtractSerializedClasses()
  {
    $stub = new SerializableTestChildStub();
    $serialized = serialize($stub);
    $this->assertEqual(lmbSerializable :: extractSerializedClasses($serialized), array('SerializableTestChildStub'));
  }

  function testRemoveIncludePathFromClassPath()
  {
    //generating class and placing it in a temp dir
    $var_dir = lmb_var_dir();
    $class = 'TestRemoveIncludePathFromClassPath' . mt_rand();
    $file_name = $class.'.php';
    file_put_contents("$var_dir/$file_name", "<?php class $class { function say() {return 'hello';} }");

    //adding temp dir to include path
    $prev_inc_path = get_include_path();
    set_include_path($var_dir . PATH_SEPARATOR . get_include_path());

    //including class and serializing it
    include($file_name);
    $foo = new $class();
    $container = new lmbSerializable($foo);
    $file = $this->_writeToFile(serialize($container));

    //now moving generated class's file into subdir
    $new_dir = mt_rand();
    mkdir("$var_dir/$new_dir");
    rename("$var_dir/$file_name", "$var_dir/$new_dir/$file_name");

    //emulating new include path settings
    $this->assertEqual($this->_phpSerializedObjectCall($file, '->say()', "$var_dir/$new_dir"), $foo->say());

    set_include_path($prev_inc_path);
  }

  function testRemoveIncludePathWithTrailingSlashFromClassPath()
  {
    //generating class and placing it in a temp dir
    $var_dir = lmb_var_dir();
    $class = 'TestRemoveIncludePathWithTrailingSlashFromClassPath' . mt_rand();
    $file_name = $class.'.php';
    file_put_contents("$var_dir/$file_name", "<?php class $class { function say() {return 'hello';} }");

    //adding temp dir to include path
    $prev_inc_path = get_include_path();
    set_include_path("$var_dir//" . PATH_SEPARATOR . get_include_path());

    //including class and serializing it
    include($file_name);
    $foo = new $class();
    $container = new lmbSerializable($foo);
    $file = $this->_writeToFile(serialize($container));

    //now moving generated class's file into subdir
    $new_dir = mt_rand();
    mkdir("$var_dir/$new_dir");
    rename("$var_dir/$file_name", "$var_dir/$new_dir/$file_name");

    //emulating new include path settings
    $this->assertEqual($this->_phpSerializedObjectCall($file, '->say()', "$var_dir/$new_dir"), $foo->say());

    set_include_path($prev_inc_path);
  }

  function testSerializingUnserializeInternalClassThrowsException()
  {
    if(class_exists('StdObject'))
    {
      $std_class = 'StdObject';
    }
    elseif(class_exists('stdClass'))
    {
      $std_class = 'stdClass';
    } else {
      echo "Notice: Could not check internal class serializing \n";
      return;
    }

    $obj = new $std_class;
    $obj->foo = "foo";
    $container = new lmbSerializable($obj);

    try
    {
      serialize($container);
      $this->fail();
    }
    catch(lmbException $e){}
  }

  function _writeToFile($serialized)
  {
    $tmp_serialized_file = lmb_var_dir() . '/serialized.' . mt_rand() . uniqid();
    file_put_contents($tmp_serialized_file, $serialized);
    return $tmp_serialized_file;
  }

  function _phpSerializedObjectCall($file, $call, $include_path = '')
  {
    $class_path = $this->_getClassPath('lmbSerializable');

    $cmd = "php -r \"require_once('$class_path');" .
           ($include_path != '' ? "set_include_path('$include_path');" : '') .
           "echo unserialize(file_get_contents('$file'))->getSubject()$call;\"";

    exec($cmd, $out, $ret);
    return implode("", $out);
  }

  function _getClassPath($class)
  {
    $ref = new ReflectionClass($class);
    return $ref->getFileName();
  }
}


