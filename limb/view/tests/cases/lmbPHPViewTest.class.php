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
lmb_require('limb/view/src/lmbPHPView.class.php');

class lmbPHPViewTest extends UnitTestCase
{
  function testRender()
  {
    file_put_contents($file = LIMB_VAR_DIR . '/tpl.php', '<?php echo "$msg, $name"; ?>');
    $template = new lmbPHPView($file);
    $template->set('msg', 'Hello');
    $template->set('name', 'world');
    $this->assertEqual($template->out(), 'Hello, world');
    unlink($file);
  }
}

?>