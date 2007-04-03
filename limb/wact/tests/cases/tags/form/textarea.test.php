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

class WactTextareaTagTestCase extends WactTemplateTestCase {

  var $Control;

  function testTextArea() {

    $template = '<form runat="server">
                        <textarea id="test" name="foo" runat="server">Some text</textarea>
                     </form>';
    $this->registerTestingTemplate('/tags/form/control/textarea/textarea.html', $template);

    $page = $this->initTemplate('/tags/form/control/textarea/textarea.html');
    $this->assertIsA($page->findChild('test'),'WactTextAreaComponent');
  }

  function testRender() {

    $template = '<form id="testForm" runat="server">
                        <textarea id="test" name="myTextarea" runat="server"></textarea>
                    </form>';
    $this->registerTestingTemplate('/components/form/textarea/render.html', $template);

    $page = $this->initTemplate('/components/form/textarea/render.html');

    $Form = $page->getChild('testForm');

    $data = new WactArrayObject(array('myTextarea' => 'foo'));

    $Form->registerDataSource($data);

    $Textarea =  $page->getChild('test');

    ob_start();
    $Textarea->renderContents();
    $out = ob_get_contents();
    ob_end_clean();

    $this->assertEqual('foo',$out);

  }

  function testRenderEntities() {

    $template = '<form id="testForm" runat="server">
                        <textarea id="test" name="myTextarea" runat="server"></textarea>
                    </form>';
    $this->registerTestingTemplate('/components/form/textarea/renderentities.html', $template);

    $page = $this->initTemplate('/components/form/textarea/renderentities.html');

    $Form = $page->getChild('testForm');

    $data = new WactArrayObject(array('myTextarea' => 'x < y > z & a'));

    $Form->registerDataSource($data);

    $Textarea = & $page->getChild('test');

    ob_start();
    $Textarea->renderContents();
    $out = ob_get_contents();
    ob_end_clean();

    $this->assertEqual('x &lt; y &gt; z &amp; a',$out);

  }


}
?>
