<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

require_once 'limb/wact/tests/cases/compiler/tag_node/WactCompilerTagTest.class.php';

Mock :: generatePartial('WactSilentCompilerTag','WactSilentCompilerTagTestVersion',
    array('generateContent'));

class WactSilentCompilerTagTest extends WactCompilerTagTest
{
  function _createNode()
  {
    return new WactSilentCompilerTag($this->source_location, 'test', $this->tag_info);
  }

  function testGenerate()
  {
    $code_writer = new WactCodeWriter();
    $this->component->generate($code_writer);
    $this->assertEqual($code_writer->renderCode(), '');
  }

  function testGenerateNow()
  {
    $component = new WactSilentCompilerTagTestVersion();
    $component->expectCallCount('generateContent', 1);
    $code_writer = new MockWactCodeWriter();
    $component->generateNow($code_writer);
  }
}

