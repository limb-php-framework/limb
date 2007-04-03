<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactDevSourceTagTest.class.php 5420 2007-03-29 12:45:34Z serega $
 * @package    wact
 */

require_once('limb/wact/tests/cases/WactTemplateTestCase.class.php');

class WactDevSourceTagTest extends WactTemplateTestCase
{
  public function testTag()
  {
    $template = '<dev:source>{$var}</dev:source>';
    $this->registerTestingTemplate('/tags/dev/source.tag', $template);

    $page = $this->initTemplate('/tags/dev/source.tag');
    $this->assertWantedPattern('~\$root-&gt;get\(\'var\'\)~', $page->capture());
  }
}
?>