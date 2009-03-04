<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

require_once 'limb/wact/src/compiler/templatecompiler.inc.php';

class WactCodeWriterTest extends UnitTestCase
{
  protected $writer;

  function setUp()
  {
    $this->writer = new WactCodeWriter();
  }

  function testGetCode()
  {
    $this->assertEqual($this->writer->renderCode(),'');
  }

  function testGetSetCode()
  {
    $this->writer->setCode($code = 'code');
    $this->assertEqual($code, $this->writer->getCode());
  }

  function testWritePHP()
  {
    $this->writer->writePHP('echo ("Hello World!");');
    $this->assertEqual($this->writer->renderCode(),'<?php echo ("Hello World!"); ?>');
  }

  function testWriteHTML()
  {
    $this->writer->writeHTML('<p>Hello World!</p>');
    $this->assertEqual($this->writer->renderCode(),'<p>Hello World!</p>');
  }

  function testSwithBetweenPHPAndHTML()
  {
    $this->writer->writePHP('echo ("Hello World!");');
    $this->writer->writeHTML('<p>Hello World!</p>');
    $this->writer->writePHP('echo ("Hello World!");');
    $this->assertEqual($this->writer->renderCode(),
                       '<?php echo ("Hello World!"); ?><p>Hello World!</p><?php echo ("Hello World!"); ?>');
  }

  function testRegisterInclude()
  {
    $this->writer->registerInclude('test.php');
    $this->assertEqual($this->writer->renderCode(),'<?php '."require_once 'test.php';\n".'?>');
  }

  function testReset()
  {
    $this->writer->writePHP('echo ("Hello World!");');
    $this->writer->registerInclude('test.php');
    $this->writer->reset();
    $this->assertEqual($this->writer->renderCode(), '');
  }

  function testBeginFunction()
  {
    $params = '($a,$b,$c)';
    $this->writer->beginFunction($params);
    $this->assertEqual($this->writer->renderCode(),'<?php function tpl1'.$params ." {\n ?>");
  }

  function testEndFunction()
  {
    $this->writer->endFunction();
    $this->assertEqual($this->writer->renderCode(),'<?php '." }\n".' ?>');
  }

  function testSetFunctionPrefix()
  {
    $this->writer->setFunctionPrefix('Test');
    $params = '($a,$b,$c)';
    $this->writer->beginFunction($params);
    $this->assertEqual($this->writer->renderCode(),'<?php function tplTest1'.$params ." {\n ?>");
  }

  function testGetTempVariable()
  {
    $var = $this->writer->getTempVariable();
    $this->assertWantedPattern('/[a-z][a-z0-9]*/i', $var);
  }

  function testGetSecondTempVariable()
  {
    $A = $this->writer->getTempVariable();
    $B = $this->writer->getTempVariable();
    $this->assertNotEqual($A, $B);
  }

  function testGetTempVariablesMany()
  {
    for ($i = 1; $i <= 30; $i++)
    {
      $var = $this->writer->getTempVariable();
      $this->assertWantedPattern('/[a-z][a-z0-9]*/i', $var);
    }
  }
}

