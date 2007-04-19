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
  function testRenderContents()
  {
    $form = new WactFormComponent('test_form');
    $text_area = new WactTextAreaComponent('my_text_area');
    $form->addChild($text_area);

    $form->registerDataSource(array('my_text_area' => 'foo'));

    ob_start();
    $text_area->renderContents();
    $out = ob_get_contents();
    ob_end_clean();

    $this->assertEqual($out, 'foo');
  }

  function testRenderEscapesHtmlEntities()
  {
    $form = new WactFormComponent('test_form');
    $text_area = new WactTextAreaComponent('my_text_area');
    $form->addChild($text_area);

    $form->registerDataSource(array('my_text_area' => 'x < y > z & a'));

    ob_start();
    $text_area->renderContents();
    $out = ob_get_contents();
    ob_end_clean();

    $this->assertEqual($out, 'x &lt; y &gt; z &amp; a');
  }
}
?>
