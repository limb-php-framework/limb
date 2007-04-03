<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactSilentCompilerTagTest.class.php 5021 2007-02-12 13:04:07Z pachanga $
 * @package    wact
 */

require_once 'limb/wact/tests/cases/compiler/tag_node/WactCompilerTagTest.class.php';

Mock :: generatePartial('WactSilentCompilerTag','WactSilentCompilerTagTestVersion',
    array('preGenerate','generateContents','postGenerate'));

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
    $component->expectCallCount('preGenerate',1);
    $component->expectCallCount('generateContents',1);
    $component->expectCallCount('postGenerate',1);
    $code_writer = new MockWactCodeWriter();
    $component->generateNow($code_writer);
  }
}
?>
