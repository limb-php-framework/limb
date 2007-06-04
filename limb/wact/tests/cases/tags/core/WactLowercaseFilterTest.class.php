<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactLowercaseFilterTest.class.php 5933 2007-06-04 13:06:23Z pachanga $
 * @package    $package$
 */

class WactLowercaseFilterTest extends WactTemplateTestCase
{
  function testLowercaseVariable()
  {
    $template = '{$test|lowercase}';
    $this->registerTestingTemplate('/filters/core/lowercase/var.html', $template);

    $page = $this->initTemplate('/filters/core/lowercase/var.html');
    $page->set('test', 'TesTing');
    $output = $page->capture();
    $this->assertEqual($output, 'testing');
  }

  function testLowerCaseSet()
  {
    $template = '<core:SET test="TesTing"/>{$test|lowercase}';
    $this->registerTestingTemplate('/filters/core/lowercase/set.html', $template);

    $page = $this->initTemplate('/filters/core/lowercase/set.html');
    $output = $page->capture();
    $this->assertEqual($output, 'testing');
  }
}
?>