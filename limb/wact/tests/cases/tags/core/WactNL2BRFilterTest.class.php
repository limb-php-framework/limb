<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactNL2BRFilterTest.class.php 5021 2007-02-12 13:04:07Z pachanga $
 * @package    wact
 */

class WactNL2BRFilterTest extends WactTemplateTestCase
{
  function testFilter()
  {
    $template = '{$test|nl2br|raw}';
    $this->registerTestingTemplate('/filters/core/text/var.html', $template);

    $page = $this->initTemplate('/filters/core/text/var.html');
    $page->set('test', "Hello\nSailor!");
    $output = $page->capture();
    $this->assertEqual($output, "Hello<br />\nSailor!");
  }
}
?>