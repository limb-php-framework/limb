<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactCoreCommentTagTest.class.php 5021 2007-02-12 13:04:07Z pachanga $
 * @package    wact
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