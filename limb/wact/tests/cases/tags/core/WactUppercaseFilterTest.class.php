<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactUppercaseFilterTest.class.php 5933 2007-06-04 13:06:23Z pachanga $
 * @package    $package$
 */

class WactUppercaseFilterTest extends WactTemplateTestCase
{
  function testUppercaseVariable()
  {
    $template = '{$test|uppercase}';
    $this->registerTestingTemplate('/filters/core/uppercase/var.html', $template);

    $page = $this->initTemplate('/filters/core/uppercase/var.html');
    $page->set('test', 'testing');
    $output = $page->capture();
    $this->assertEqual($output, 'TESTING');
  }

  function testUpperCaseSet()
  {
    $template = '<core:SET test="testing"/>{$test|uppercase}';
    $this->registerTestingTemplate('/filters/core/uppercase/set.html', $template);

    $page = $this->initTemplate('/filters/core/uppercase/set.html');
    $output = $page->capture();
    $this->assertEqual($output, 'TESTING');
  }
}
?>