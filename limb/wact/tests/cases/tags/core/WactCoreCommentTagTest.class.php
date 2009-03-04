<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
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

