<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

class lmbMacroTemplateExecutorTest extends lmbBaseMacroTest
{
  function testPassVars()
  {
    $tpl = new lmbMacroTemplateExecutor($this->_createMacroConfig(), array('foo' => 'foo', 'bar' => 'bar'));
    $tpl->set('zoo', 'zoo');
    $this->assertEqual($tpl->foo, 'foo');
    $this->assertEqual($tpl->bar, 'bar');
    $this->assertEqual($tpl->zoo, 'zoo');
  }

  function testMissingVarIsEmpty()
  {
    $tpl = new lmbMacroTemplateExecutor($this->_createMacroConfig());
    $this->assertNoErrors();
    $this->assertIdentical($tpl->junk, '');
  }
}

