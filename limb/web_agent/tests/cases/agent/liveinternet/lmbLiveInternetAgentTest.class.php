<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * @package web_agent
 */
lmb_require('limb/web_agent/src/agent/liveinternet/lmbLiveInternetAgent.class.php');

/**
 * @package web_agent
 * @version $Id: lmbLiveInternetAgentTest.class.php 89 2007-10-12 15:28:50Z CatMan $
 */
class lmbLiveInternetAgentTest extends UnitTestCase {
  protected $agent;
  protected $request;

  function setUp()
  {
    $this->request = new lmbFakeWebAgentRequest();
  	$this->agent = new lmbLiveInternetAgent('test.ru', $this->request);
  }

  function testGetProject()
  {
    $this->assertEqual($this->agent->getProject(), 'test.ru');
  }

  function testGetValues()
  {
    $arr = array(
      'test' => 'val',
      'test1' => 'val1',
      'id' => array(9,7,5,0));
    $vals = $this->agent->getValues();
    $vals->import($arr);

    $this->assertEqual($vals->buildQuery(), 'test=val;test1=val1;id=9;id=7;id=5;id=0');
  }

  function testRequestStatPage()
  {
  	$this->agent->requestStatPage('visitors.html');

    $this->assertEqual($this->request->request_url, 'http://www.liveinternet.ru/stat/test.ru/visitors.html');
  }

  function testAuth()
  {
    $this->request->response_cookies->add(new lmbWebServerCookie('sid=zxc'));
  	$this->agent->auth('***');

    $this->assertEqual($this->request->request_url, 'http://www.liveinternet.ru/stat/test.ru/');
    $this->assertEqual($this->request->request_content,
      http_build_query(array('url' => 'http://test.ru', 'password' => '***', 'ok' => ' ok ')));
    $this->assertEqual($this->agent->getCookies()->get(0)->value, 'zxc');
  }
}
