<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactCoreRuntimeContentTagTest.class.php 5188 2007-03-06 07:42:21Z serega $
 * @package    wact
 */

require_once 'limb/wact/src/widgets/widgets.inc.php';

class WactCoreRuntimeContentTagTest extends WactTemplateTestCase
{
  function testRuntimeContent()
  {
    $template = '-<core:runtimecontent id="slot"/>-';

    $this->registerTestingTemplate('/tags/core/runtimecontent/test.html', $template);
    $page = $this->initTemplate('/tags/core/runtimecontent/test.html');

    $holder = $page->getChild('slot');
    $holder->addChild(new WactTextWidget('inserted'));

    $output = $page->capture();
    $this->assertEqual($output, '-inserted-');
  }
}
?>