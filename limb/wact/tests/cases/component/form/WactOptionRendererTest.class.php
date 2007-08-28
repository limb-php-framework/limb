<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

require_once 'limb/wact/src/components/form/form.inc.php';
require_once 'limb/wact/src/components/form/WactOptionRenderer.class.php';

class WactOptionRendererTest extends UnitTestCase
{
  protected $renderer;

  function setUp()
  {
    $this->renderer=  new WactOptionRenderer();
  }

  function testRender()
  {
    ob_start();
    $this->renderer->renderOption('foo','bar',FALSE);
    $out = ob_get_contents();
    ob_end_clean();
    $this->assertEqual($out,'<option value="foo">bar</option>');
  }

  function testRenderNoContents()
  {
    ob_start();
    $this->renderer->renderOption('foo','',$selected = FALSE);
    $out = ob_get_contents();
    ob_end_clean();
    $this->assertEqual($out,'<option value="foo">foo</option>');
  }

  function testRenderEntities()
  {
    ob_start();
    $this->renderer->renderOption('x > y','& v < z',$selected = FALSE);
    $out = ob_get_contents();
    ob_end_clean();
    $this->assertEqual($out,'<option value="x &gt; y">&amp; v &lt; z</option>');
  }

  function testRenderEntitiesNoContents()
  {
    ob_start();
    $this->renderer->renderOption('x > y', FALSE, $selected = FALSE);
    $out = ob_get_contents();
    ob_end_clean();
    $this->assertEqual($out,'<option value="x &gt; y">x &gt; y</option>');
  }

  function testSelected()
  {
    ob_start();
    $this->renderer->renderOption('foo','bar',TRUE);
    $out = ob_get_contents();
    ob_end_clean();
    $this->assertEqual($out,'<option value="foo" selected="true">bar</option>');
  }
}

