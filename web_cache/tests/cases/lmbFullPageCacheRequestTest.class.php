<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/web_cache/src/lmbFullPageCacheUser.class.php');
lmb_require('limb/web_cache/src/lmbFullPageCacheRequest.class.php');
lmb_require('limb/net/src/lmbHttpRequest.class.php');

class lmbFullPageCacheRequestTest extends UnitTestCase
{
  function testGetHash()
  {
    $user = new lmbFullPageCacheUser();
    $http_request = new lmbHttpRequest('http://test.com', array(), array(), array(), array());

    $request = new lmbFullPageCacheRequest($http_request, $user);

    $this->assertEqual($request->getHash(), '/');
  }

  function testGetHashAlphabeticSorting()
  {
    $user = new lmbFullPageCacheUser(array(2 => 'test', 3 => 'admin'));
    $http_request = new lmbHttpRequest('http://test.com/path?z=3&a=1&c[d]=2', array(), array(), array(), array());

    $request = new lmbFullPageCacheRequest($http_request, $user);

    $this->assertEqual($request->getHash(),
                       '/path_' . md5(serialize(array('a' => '1', 'c[d]' => '2', 'z' => '3')) .
                                     serialize(array('admin', 'test'))) . '/');
  }
}


