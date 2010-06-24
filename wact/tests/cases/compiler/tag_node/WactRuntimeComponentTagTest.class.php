<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

require_once('limb/wact/tests/cases/compiler/tag_node/WactCompilerTagTest.class.php');

class WactRuntimeComponentTagTestVersion extends WactRuntimeComponentTag
{
  public $runtimeIncludeFile = 'testinclude.inc.php';
  public $runtimeComponentName = 'testcomponent';
}

Mock :: generatePartial('WactCodeWriter', 'WactCodeWriterTestVersion', array('getTempVariable',
                                                                             'registerInclude'));

class WactRuntimeComponentTagTest extends WactCompilerTagTest
{
  function _createNode()
  {
    $component =  new WactRuntimeComponentTagTestVersion($this->source_location, 'test', $this->tag_info);
    $component->setServerId('id001');
    return $component;
  }

  function testGetServerId()
  {
    $this->assertEqual($this->component->getServerId(),'id001');
  }

  function testGetServerIdAttribute()
  {
    $this->assertEqual($this->component->getServerId(),'id001');
  }

  function testGetComponentRefCode()
  {
    $Mock = new MockWactCompileTreeNode();
    $Mock->setReturnValue('getComponentRefCode','$DataSpace');
    $this->component->parent = $Mock;

    $code_writer = new WactCodeWriterTestVersion();
    $code_writer->setReturnValue('getTempVariable', 'AA');

    $this->component->generateUniqueId($code_writer);

    $this->assertEqual($this->component->getComponentRefCode(),'$components[\'AA\']');
  }

  function testGetServerIdFromId()
  {
    $this->assertEqual($this->component->getServerId(), 'id001');
  }

  function testGetServerIdFromWactId()
  {
    $this->assertEqual($this->component->getServerId(), 'id001');
  }

  function testGenerateConstructor()
  {
    $this->component->runtimeIncludeFile = 'testinclude.inc.php';
    $this->component->runtimeComponentName = 'testname';

    $MockCode = new WactCodeWriterTestVersion();
    $MockCode->expectOnce('registerInclude', array('testinclude.inc.php'));
    $MockCode->setReturnValue('getTempVariable', 'AA');

    $MockParent = new MockWactCompileTreeNode();
    $MockParent->setReturnValue('getComponentRefCode','$DataSpace');

    $this->component->parent = $MockParent;
    $this->component->generateConstructor($MockCode);

    $this->assertEqual($MockCode->renderCode(),
                       '<?php $AA = new testname(\'id001\');'. "\n" .
                       '$components[\'AA\'] = $AA;'. "\n" .
                       '$DataSpace->addChild($AA);'. "\n" . ' ?>');
  }
}


