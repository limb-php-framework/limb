<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
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

