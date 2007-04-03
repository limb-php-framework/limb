<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbFullPageCacheFilterTest.class.php 5424 2007-03-29 13:10:47Z pachanga $
 * @package    web_cache
 */
lmb_require('limb/filter_chain/src/lmbInterceptingFilter.interface.php');
lmb_require('limb/filter_chain/src/lmbFilterChain.class.php');
lmb_require('limb/web_cache/src/lmbFullPageCacheUser.class.php');
lmb_require('limb/web_cache/src/filter/lmbFullPageCacheFilter.class.php');
lmb_require('limb/net/src/lmbHttpRequest.class.php');
lmb_require('limb/config/src/lmbFakeIni.class.php');

Mock :: generate('lmbInterceptingFilter', 'MockInterceptingFilter');

class lmbFullPageCacheFilterTest extends UnitTestCase
{
  protected $fc;
  protected $filter2;
  protected $toolkit;
  protected $user;

  function setUp()
  {
    lmbFs :: rm(lmbFullPageCacheFilter :: getCacheDir());
    $this->filter2 = new MockInterceptingFilter();
    $this->user = new lmbFullPageCacheUser();
    $this->toolkit = lmbToolkit :: save();
  }

  function tearDown()
  {
    lmbToolkit :: restore();
  }

  function testRunOkFullCircle()
  {
    $filter = new lmbFullPageCacheFilter($this->user);

    $fc = new lmbFilterChain();
    $fc->registerFilter($filter);
    $fc->registerFilter($this->filter2);

    $rules = '
     [rull-all-to-all]
     path_regex = ~^.*$~
     policy = allow
    ';
    $this->toolkit->setConf(lmbFullPageCacheFilter :: getRulesIni(), new lmbFakeIni($rules));

    $this->filter2->expectOnce('run');

    $response = $this->toolkit->getResponse();
    $response->start();
    $response->write('some_content'); // I don't want to create a stub for filter2
                                      // to write something to response. I'd like to it here.

    $this->toolkit->setRequest(new lmbHttpRequest('/any_path'));

    $fc->process();

    $response->reset();
    $response->start();

    $fc->process();
    $this->assertEqual($response->getResponseString(), 'some_content');
  }
}

?>
