<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

require_once('limb/wact/src/components/form/form.inc.php');

class WactFormRefererTagTest extends WactTemplateTestCase
{
  protected $old_server;

  function setUp()
  {
    parent :: setUp();
    if(isset($_SERVER))
      $this->old_server = $_SERVER;
  }

  function tearDown()
  {
    if($this->old_server)
      $_SERVER = $this->old_server;
    parent :: tearDown();
  }

  function testRefererFormNotSubmitted()
  {
    $template = "<form name='test' runat='server'><form:REFERER/></form>";

    $this->registerTestingTemplate('/form/form_referer/ref1.html', $template);

    $page = $this->initTemplate('/form/form_referer/ref1.html');

    $referer = 'put-me-into-result';
    $_SERVER['HTTP_REFERER'] = $referer;

    $result = $page->capture();
    $this->assertEqual($result, "<form name=\"test\"><input type='hidden' name='referer' value='$referer'></form>");
  }

  function testRefererFormNotSubmittedNoReferer()
  {
    $template = "<form name='test' runat='server'><form:REFERER/></form>";

    $this->registerTestingTemplate('/form/form_referer/ref2.html', $template);

    $page = $this->initTemplate('/form/form_referer/ref2.html');

    $_SERVER['HTTP_REFERER'] = null;

    $result = $page->capture();
    $this->assertEqual($result,
                       "<form name=\"test\"></form>");
  }

  function testRefererFormSubmitted()
  {
    $template = "<form id='test' runat='server'><form:REFERER/></form>";

    $this->registerTestingTemplate('/form/form_referer/ref3.html', $template);

    $page = $this->initTemplate('/form/form_referer/ref3.html');

    $referer = 'put-me-into-result';

    $form = $page->getChild('test');
    $form->registerDataSource(array('referer' => $referer));

    $_SERVER['HTTP_REFERER'] = 'another-referer';

    $result = $page->capture();
    $this->assertEqual($result, "<form id=\"test\"><input type='hidden' name='referer' value='$referer'></form>");
  }

  function testRefererFormNotSubmittedUseCurrent()
  {
    $template = "<form name='test' runat='server'><form:REFERER use_current='TRUE'/></form>";

    $this->registerTestingTemplate('/form/form_referer/ref4.html', $template);

    $page = $this->initTemplate('/form/form_referer/ref4.html');

    $referer = 'put-me-into-result';
    $_SERVER['HTTP_REFERER'] = 'another-referer';
    $_SERVER['REQUEST_URI'] = $referer;

    $result = $page->capture();
    $this->assertEqual($result,
                       "<form name=\"test\"><input type='hidden' name='referer' value='$referer'></form>");
  }

  function testRefererFormSubmittedUseCurrent()
  {
    $template = "<form id='test' runat='server'><form:REFERER use_current='TRUE'/></form>";

    $this->registerTestingTemplate('/form/form_referer/ref5.html', $template);

    $page = $this->initTemplate('/form/form_referer/ref5.html');

    $referer = 'put-me-into-result';

    $form = $page->getChild('test');
    $form->registerDataSource(array('referer'=> $referer));

    $_SERVER['HTTP_REFERER'] = 'another-referer';
    $_SERVER['REQUEST_URI'] = 'another-referer';

    $result = $page->capture();
    $this->assertEqual($result,
                       "<form id=\"test\"><input type='hidden' name='referer' value='$referer'></form>");
  }

}

