<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

require_once('limb/wact/tests/cases/WactTemplateTestCase.class.php');

if(!class_exists('WactRuntimeComponentTest'))
{

class WactRuntimeComponentTest extends WactTemplateTestCase
{
  protected $component;

  function setUp()
  {
    parent :: setUp();
    $this->component = new WactRuntimeComponent('TestId');
  }

  function testGetServerID()
  {
    $this->assertEqual($this->component->getId(),'TestId');
  }

  function testFindChild()
  {
    $child = new WactRuntimeComponent('TestChild');
    $this->component->addChild($child);
    $this->assertReference($this->component->findChild('TestChild'), $child);
  }

  function testFindChildNotFound()
  {
    $this->assertFalse($this->component->findChild('TestChild'));
  }

  function testFindChildByClass()
  {
    $child = new WactRuntimeComponent('TestChild');
    $this->component->addChild($child);
    $this->assertReference($this->component->findChildByClass('WactRuntimeComponent'), $child);
  }

  function testFindChildByClassNotFound()
  {
    $this->assertFalse($this->component->findChildByClass('TestComponent'));
  }

  function testFindParentByChilld()
  {
    $component = new WactRuntimeComponent('TestParent');
    $component->addChild($this->component);
    $this->assertIsA($this->component->findParentByClass('WactRuntimeComponent'),'WactRuntimeComponent');
  }

  function testFindParentByClassNotFound()
  {
    $this->assertFalse($this->component->findParentByClass('TestComponent'));
  }
}

}

