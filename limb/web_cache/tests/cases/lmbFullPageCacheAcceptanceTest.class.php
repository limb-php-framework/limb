<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbFullPageCacheAcceptanceTest.class.php 5424 2007-03-29 13:10:47Z pachanga $
 * @package    web_cache
 */
lmb_require('limb/net/src/lmbUri.class.php');
lmb_require('limb/web_cache/src/lmbFullPageCacheRuleset.class.php');
lmb_require('limb/web_cache/src/lmbFullPageCachePolicy.class.php');
lmb_require('limb/web_cache/src/lmbFullPageCache.class.php');
lmb_require('limb/web_cache/src/lmbFullPageCacheUser.class.php');
lmb_require('limb/web_cache/src/lmbFullPageCacheWriter.class.php');
lmb_require('limb/web_cache/src/lmbFullPageCacheIniPolicyLoader.class.php');
lmb_require('limb/web_cache/src/lmbFullPageCacheRequest.class.php');
lmb_require('limb/net/src/lmbHttpRequest.class.php');
lmb_require('limb/config/src/lmbFakeIni.class.php');

class lmbFullPageCacheAcceptanceTest extends UnitTestCase
{
  protected $toolkit;
  protected $ruleset;
  protected $cache_writer;

  function setUp()
  {
    $this->toolkit = lmbToolkit :: save();

    $this->cache_writer = new lmbFullPageCacheWriter();
    $this->cache_writer->setStorageDirectory(dirname(__FILE__) . '/../var/pages');
    $this->cache_writer->flushAll();
  }

  function tearDown()
  {
    $this->cache_writer->flushAll();
    lmbToolkit :: restore();
  }

  function testAll()
  {
    $this->_registerRules('[non-matching-rule]
                            path_regex = ~no-match~

                           [matching-rule]
                           path_regex = ~path~
                           request[id1] = *
                           request[id2] = *'
                           );

    $user = new lmbFullPageCacheUser();
    $http_request = new lmbHttpRequest('http://dot.com/path?id1=test1&id2=test2', array(), array());
    $valid_request = new lmbFullPageCacheRequest($http_request, $user);

    $cache = new lmbFullPageCache($this->cache_writer, $this->policy);

    //first time reading
    $this->assertTrue($cache->openSession($valid_request));
    $this->assertFalse($cache->get());
    $cache->save($content = 'test');

    //repeated reading
    $this->assertTrue($cache->openSession($valid_request));
    $this->assertTrue($cache->get());

    //invalid request
    $user = new lmbFullPageCacheUser();
    $http_request = new lmbHttpRequest('http://dot.com', array(), array());
    $invalid_request = new lmbFullPageCacheRequest($http_request, $user);

    $this->assertFalse($cache->openSession($invalid_request));
  }

  function _registerRules($content)
  {
    $this->toolkit->setConf('cache.ini', new lmbFakeIni($content));

    $loader = new lmbFullPageCacheIniPolicyLoader('cache.ini');
    $this->policy = $loader->load();
  }
}

?>
