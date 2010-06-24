<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
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

