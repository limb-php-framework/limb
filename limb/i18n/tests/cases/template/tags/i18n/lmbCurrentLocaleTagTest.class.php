<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbCurrentLocaleTagTest.class.php 5373 2007-03-28 11:10:40Z pachanga $
 * @package    web_app
 */

class lmbCurrentLocaleTagTest extends lmbWactTestCase
{
  function testUseEnglishLocale()
  {
    $this->toolkit->setLocale('en');
    $template = '<limb:current_locale name="en">Some text</limb:current_locale>' .
                '<limb:current_locale name="ru">Other text</limb:current_locale>';

    $this->registerTestingTemplate('/limb/locale_default.html', $template);

    $page = $this->initTemplate('/limb/locale_default.html');

    $this->assertEqual($page->capture(), 'Some text');
  }

  function testUseRussianLocale() // Just to be sure
  {
    $this->toolkit->setLocale('ru');
    $template = '<limb:current_locale name="ru">Some text</limb:current_locale>' .
                '<limb:current_locale name="en">Other text</limb:current_locale>';

    $this->registerTestingTemplate('/limb/locale_default_use_russian.html', $template);

    $page = $this->initTemplate('/limb/locale_default_use_russian.html');

    $this->assertEqual($page->capture(), 'Some text');
  }
}
?>
