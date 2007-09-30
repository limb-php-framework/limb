<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

lmb_require('limb/macro/src/lmbMacroTemplateExecutor.class.php'); 

class lmbMacroTemplateExecutorTest extends UnitTestCase
{
  function testPassVars()
  {
    $tpl = new lmbMacroTemplateExecutor(new lmbMacroConfig(), array('foo' => 'foo', 'bar' => 'bar'));
    $tpl->set('zoo', 'zoo');
    $this->assertEqual($tpl->foo, 'foo');
    $this->assertEqual($tpl->bar, 'bar');
    $this->assertEqual($tpl->zoo, 'zoo');
  }

  function testMissingVarIsEmpty()
  {
    $tpl = new lmbMacroTemplateExecutor(new lmbMacroConfig());
    $this->assertNoErrors();
    $this->assertIdentical($tpl->junk, '');
  }
}

