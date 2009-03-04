<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

require_once('limb/wact/tests/cases/compiler/tag_node/WactRuntimeComponentTagTest.class.php');

class WactRuntimeComponentHTMLTagTestVersion extends WactRuntimeComponentHTMLTag
{
  public $runtimeIncludeFile = 'testinclude.inc.php';
  public $runtimeComponentName = 'testcomponent';
}

Mock::generate('WactRuntimeComponentHTMLTag','MockWactRuntimeComponentHTMLTag');

Mock :: generatePartial('WactCodeWriter', 'TestingWactCodeWriter', array('getTempVariable'));

class WactRuntimeComponentHTMLTagTest extends WactRuntimeComponentTagTest
{
  protected $tag_info;

  function _createNode()
  {
    $this->tag_info = new WactTagInfo('test', 'WactRuntimeComponentHTMLTagTestVersion');

    $component = new WactRuntimeComponentHTMLTagTestVersion($this->source_location , 'test', $this->tag_info);
    $component->setServerId('id001');
    $component->hasClosingTag = TRUE;

    $MockParent = new MockWactCompileTreeNode();
    $MockParent->setReturnValue('getComponentRefCode', '$root');
    $MockParent->parent = null;

    $component->parent = $MockParent;
    return $component;
  }

  function testGetRenderedTag()
  {
    $this->assertEqual($this->component->getRenderedTag(), 'test');
  }

  function testGenerateBeforeContent()
  {
    $code_writer = new TestingWactCodeWriter();
    $code_writer->setReturnValue('getTempVariable', 'AA');

    $this->component->generateUniqueId($code_writer);

    $this->component->generateBeforeContent($code_writer);
    $this->assertEqual($code_writer->renderCode(),'<test<?php $components[\'AA\']->renderAttributes(); ?>>');
  }

  function testPostGenerate()
  {
    $Code = new WactCodeWriter();
    $this->component->generateAfterContent($Code);
    $this->assertEqual($Code->renderCode(), '</test>');
  }

  function testGenerateConstructor()
  {
    $Code = new WactCodeWriter();
    $this->component->generateConstructor($Code);
  }
}

