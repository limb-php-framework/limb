<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

class lmbMacroCodeWriterTest extends lmbBaseMacroTest
{
  protected $writer;

  function setUp()
  {
    parent::setUp();
    $this->class = 'Foo' . mt_rand();
    $this->writer = new lmbMacroCodeWriter($this->class);
  }

  function testRenderEmpty()
  {
    $object = $this->_instantiate();
    $this->assertIsA($object, 'lmbMacroTemplateExecutor');
    $this->assertNull($object->render());
  }

  function testWritePHP()
  {
    $this->writer->writePHP('return "Hello World!";');
    $object = $this->_instantiate();
    $this->assertEqual($object->render(), 'Hello World!');
  }

  function testWriteHTML()
  {
    $this->writer->writeHTML('<p>Hello World!</p>');
    $this->assertEqual($this->_render(),'<p>Hello World!</p>');
  }

  function testSwithBetweenPHPAndHTML()
  {
    $this->writer->writePHP('echo ("Hello World!");');
    $this->writer->writeHTML('<p>Hello World!</p>');
    $this->writer->writePHP('echo ("Hello World!");');

    $this->assertEqual($this->_render(), "Hello World!<p>Hello World!</p>Hello World!");
  }

  function testFunction()
  {
    $params = array('$a', '$b');
    $func = $this->writer->beginFunction('tpl' . mt_rand(), $params);
    $this->writer->writePHP('echo $a . $b;');
    $this->writer->endFunction();
    $this->writer->writePHP("$func('a', 'b');");

    $this->assertEqual($this->_render(), 'ab');
  }

  function testMethod()
  {
    $params = array('$a', '$b');
    $func = $this->writer->beginMethod('tpl' . mt_rand(), $params);
    $this->writer->writePHP('echo $a . $b;');
    $this->writer->endMethod();
    $this->writer->writePHP("\$this->$func('a', 'b');");

    $this->assertEqual($this->_render(), 'ab');
  }

  function testNestedMethods()
  {
    $params = array('$a', '$b');
    //inside fooxxx method
    $foo = $this->writer->beginMethod('foo' . mt_rand(), $params);

    //inside barxxx method, note, we're inside fooxxx as well
    $bar = $this->writer->beginMethod('bar' . mt_rand(), $params);
    $this->writer->writePHP('return $b . $a;');
    $this->writer->endMethod();

    $this->writer->writePHP('return $a . $b . ');//contecanating with barxxx method
    $this->writer->writePHP("\$this->$bar(\$a, \$b);");
    $this->writer->endMethod();

    $this->writer->writePHP("echo \$this->$foo('a', 'b');");

    $this->assertEqual($this->_render(), 'abba');
  }

  function testWriteIntoConstructor()
  {
    $bar = $this->writer->beginMethod('bar' . mt_rand());
    $this->writer->writePHP('echo "b-b-b";');
    $this->writer->endMethod();

    $foo = $this->writer->beginMethod('foo' . mt_rand());
    $this->writer->writePHP('echo "a-a-a";');
    $this->writer->endMethod();

    $this->writer->writePHP("\$this->$bar();");
    $this->writer->writeToInit("\$this->$foo();");

    $this->assertEqual($this->_render(), 'a-a-ab-b-b');
  }

  function testgenerateTempName()
  {
    $var = $this->writer->generateTempName();
    $this->assertWantedPattern('/[a-z][a-z0-9]*/i', $var);
  }

  function testGetSecondTempVariable()
  {
    $A = $this->writer->generateTempName();
    $B = $this->writer->generateTempName();
    $this->assertNotEqual($A, $B);
  }

  function testgenerateTempNamesMany()
  {
    for($i = 1; $i <= 300; $i++)
    {
      $var = $this->writer->generateTempName();
      $this->assertWantedPattern('/[a-z][a-z0-9]*/i', $var);
    }
  }

  function _render()
  {
    $object = $this->_instantiate();
    ob_start();
    $object->render();
    $out = ob_get_contents();
    ob_end_clean();
    return $out;
  }

  function _instantiate()
  {
    $this->_writeAndInclude($this->writer->renderCode());
    $class = $this->class;
    $object = new $class($this->_createMacroConfig());
    return $object;
  }

  function _writeAndInclude($code)
  {
    file_put_contents($file = LIMB_VAR_DIR . '/' . mt_rand() . '.php', $code);
    include($file);
    unlink($file);
  }
}

