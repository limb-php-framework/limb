<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactCoreRuntimeContentTagTest.class.php 5933 2007-06-04 13:06:23Z pachanga $
 * @package    $package$
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