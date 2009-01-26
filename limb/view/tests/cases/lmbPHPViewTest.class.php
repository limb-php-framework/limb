<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
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
    $this->assertEqual($template->render(), 'Hello, world');
    unlink($file);
  }
}


