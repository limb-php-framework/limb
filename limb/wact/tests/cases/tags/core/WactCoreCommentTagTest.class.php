<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactCoreCommentTagTest.class.php 5933 2007-06-04 13:06:23Z pachanga $
 * @package    $package$
 */

class WactCoreCommentTagTest extends WactTemplateTestCase
{
  function testRenderNothing()
  {
    $template = '<core:comment>Comment comment</core:comment>';
    $this->registerTestingTemplate('/tags/core/comment/comment.html', $template);

    $page = $this->initTemplate('/tags/core/comment/comment.html');
    $this->assertEqual($page->capture(), '');
  }
}
?>