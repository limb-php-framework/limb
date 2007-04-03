<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactNoTagsFilterTest.class.php 5189 2007-03-06 08:06:16Z serega $
 * @package    wact
 */

class WactNoTagsFilterTest extends WactTemplateTestCase
{
  function testNoTagsVariable()
  {
    $template = '{$test|notags}';
    $this->registerTestingTemplate('/filters/core/notags/var.html', $template);

    $page = $this->initTemplate('/filters/core/notags/var.html');
    $page->set('test', '<hello>dude!');
    $output = $page->capture();
    $this->assertEqual($output, 'dude!');
  }

  function testNoTagsSet()
  {
    $template = '<core:SET test="<hello>dude!"/>{$test|notags}';
    $this->registerTestingTemplate('/filters/core/notags/set.html', $template);

    $page = $this->initTemplate('/filters/core/notags/set.html');
    $output = $page->capture();
    $this->assertEqual($output, 'dude!');
  }
}
?>