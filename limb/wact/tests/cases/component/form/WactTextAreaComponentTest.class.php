<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: textarea.test.php 5021 2007-02-12 13:04:07Z pachanga $
 * @package    wact
 */

require_once('limb/wact/tests/cases/WactTemplateTestCase.class.php');
require_once('limb/wact/src/components/form/form.inc.php');

class WactTextAreaComponentTest extends WactTemplateTestCase
{
  protected $text_area;
  protected $form;

  function setUp()
  {
    parent :: setUp();

    $this->form = new WactFormComponent('test_form');
    $this->text_area = new WactTextAreaComponent('my_text_area');
    $this->form->addChild($this->text_area);
  }

  function testRenderContents()
  {
    $this->form->set('my_text_area', 'foo');

    ob_start();
    $this->text_area->renderContents();
    $out = ob_get_contents();
    ob_end_clean();

    $this->assertEqual($out, 'foo');
  }

  function testRenderEscapesHtmlEntities()
  {
    $this->form->set('my_text_area', 'x < y > z & a');

    ob_start();
    $this->text_area->renderContents();
    $out = ob_get_contents();
    ob_end_clean();

    $this->assertEqual($out, 'x &lt; y &gt; z &amp; a');
  }
}
?>
