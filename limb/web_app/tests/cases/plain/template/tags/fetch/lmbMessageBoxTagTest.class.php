<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbRouteUrlTagTest.class.php 5287 2007-03-20 08:39:30Z serega $
 * @package    web_app
 */

class lmbMessageBoxTagTest extends lmbWactTestCase
{
  function _testPassFlashBoxMessagesToListByTargetAttribute()
  {
    $template = '<message_box target="flash_box"/>'.
                 '<list:list id="flash_box"><list:ITEM >'.
                 '{$text}|'.
                 '</list:ITEM></list:list>';

    $this->toolkit->flashError('Error1');
    $this->toolkit->flashError('Error2');

    $this->registerTestingTemplate('/limb/flash_box/with_target.html', $template);

    $page = $this->initTemplate('/limb/flash_box/with_target.html');

    $this->assertEqual($page->capture(), 'Error1|Error2|');
  }
}
?>
