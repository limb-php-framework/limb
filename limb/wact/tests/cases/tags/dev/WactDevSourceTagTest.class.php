<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

require_once('limb/wact/tests/cases/WactTemplateTestCase.class.php');

class WactDevSourceTagTest extends WactTemplateTestCase
{
  public function testTag()
  {
    $template = '<dev:source>{$var}</dev:source>';
    $this->registerTestingTemplate('/tags/dev/source.tag', $template);

    $page = $this->initTemplate('/tags/dev/source.tag');
    $this->assertWantedPattern('~WactTemplate::getValue\(\$root-&gt;datasource,\'var\'\)~', $page->capture());
  }
}

