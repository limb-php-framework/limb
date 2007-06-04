<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id$
 * @package    $package$
 */

class lmbFlashBoxTagTest extends lmbWactTestCase
{
  function tearDown()
  {
    $this->toolkit->getFlashBox()->reset();
    parent :: tearDown();
  }

  function testPassFlashBoxMessagesToListByTargetAttribute()
  {
    $template = '<flash_box target="flash_box"/>'.
                 '<list:list id="flash_box"><list:ITEM>'.
                 '{$message}|'.
                 '</list:ITEM></list:list>';

    $this->toolkit->flashError('Error1');
    $this->toolkit->flashError('Error2');

    $this->registerTestingTemplate('/limb/flash_box/with_target.html', $template);

    $page = $this->initTemplate('/limb/flash_box/with_target.html');

    $this->assertEqual($page->capture(), 'Error1|Error2|');
  }

  function testErrorsOnlyIfErrorAttributePresents()
  {
    $template = '<flash_box target="flash_box" errors="true"/>'.
                 '<list:list id="flash_box"><list:ITEM>'.
                 '{$message}|'.
                 '</list:ITEM></list:list>';

    $this->toolkit->flashError('Error1');
    $this->toolkit->flashError('Error2');
    $this->toolkit->flashMessage('Message1');
    $this->toolkit->flashMessage('Message2');

    $this->registerTestingTemplate('/limb/flash_box/errors_only.html', $template);

    $page = $this->initTemplate('/limb/flash_box/errors_only.html');

    $this->assertEqual($page->capture(), 'Error1|Error2|');
  }

  function testMessagesOnlyIfMessageAttributePresents()
  {
    $template = '<flash_box target="flash_box" messages="true"/>'.
                 '<list:list id="flash_box"><list:ITEM>'.
                 '{$message}|'.
                 '</list:ITEM></list:list>';

    $this->toolkit->flashError('Error1');
    $this->toolkit->flashError('Error2');
    $this->toolkit->flashMessage('Message1');
    $this->toolkit->flashMessage('Message2');

    $this->registerTestingTemplate('/limb/flash_box/messages_only.html', $template);

    $page = $this->initTemplate('/limb/flash_box/messages_only.html');

    $this->assertEqual($page->capture(), 'Message1|Message2|');
  }
}
?>
