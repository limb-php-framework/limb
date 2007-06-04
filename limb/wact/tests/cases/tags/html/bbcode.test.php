<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: bbcode.test.php 5933 2007-06-04 13:06:23Z pachanga $
 * @package    $package$
 */

require_once 'limb/wact/src/components/html/bbcode.inc.php';

class WactHtmlBBCodeComponentTestCase extends WactTemplateTestCase
{
  function testJavascriptInjection()
  {
    $template = '<html:bbcode id="test">';
    $this->registerTestingTemplate('/components/html/bbcode/javascriptinjection.html', $template);

    $page = $this->initTemplate('/components/html/bbcode/javascriptinjection.html');

    $BBCode = $page->getChild('test');
    $BBCode->setText('[url=javascript:alert(\'Hacker!\')]Click Here[/url]');
    $res = $page->capture();
    $this->assertNoUnwantedPattern('~javascript~',$res);
  }
}
?>
