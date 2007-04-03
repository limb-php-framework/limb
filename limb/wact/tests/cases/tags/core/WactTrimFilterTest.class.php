<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactTrimFilterTest.class.php 5021 2007-02-12 13:04:07Z pachanga $
 * @package    wact
 */

class WactTrimFilterTest extends WactTemplateTestCase
{
  function testSimple()
  {
    $template = '{$" value "|trim}';

    $this->registerTestingTemplate('/filters/core/trim/simple.html', $template);

    $page = $this->initTemplate('/filters/core/trim/simple.html');

    $this->assertEqual($page->capture(), 'value');
  }

  function testCharacters()
  {
    $template = '{$"$:value:$"|trim:"$"}';

    $this->registerTestingTemplate('/filters/core/trim/characters.html', $template);

    $page = $this->initTemplate('/filters/core/trim/characters.html');

    $this->assertEqual($page->capture(), ':value:');
  }

  function testDBE()
  {
    $template = '{$var|trim}';

    $this->registerTestingTemplate('/filters/core/trim/dbe.html', $template);

    $page = $this->initTemplate('/filters/core/trim/dbe.html');
    $page->set('var', ' value ');

    $this->assertEqual($page->capture(), 'value');
  }

  function testDBECharacters()
  {
    $template = '{$var|trim: "$"}';

    $this->registerTestingTemplate('/filters/core/trim/dbe_characters.html', $template);

    $page = $this->initTemplate('/filters/core/trim/dbe_characters.html');
    $page->set('var', '$:value:$');

    $this->assertEqual($page->capture(), ':value:');
  }
}
?>