<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/web_cache/src/lmbFullPageCacheRequestRule.class.php');
lmb_require('limb/web_cache/src/lmbFullPageCacheRequest.class.php');
lmb_require('limb/web_cache/src/lmbFullPageCacheUser.class.php');
lmb_require('limb/net/src/lmbHttpRequest.class.php');

class lmbFullPageCacheRequestRuleTest extends UnitTestCase
{
  function testRequestGlobMatch()
  {
    $rule = new lmbFullPageCacheRequestRule('*');

    $http_request = new lmbHttpRequest('http://dot.com?req1=1', array(), array());
    $user = new lmbFullPageCacheUser();
    $request = new lmbFullPageCacheRequest($http_request, $user);

    $this->assertTrue($rule->isSatisfiedBy($request));
  }

  function testRequestGlobNonmatch()
  {
    $rule = new lmbFullPageCacheRequestRule('*');

    $http_request = new lmbHttpRequest('http://dot.com', array(), array());
    $user = new lmbFullPageCacheUser();
    $request = new lmbFullPageCacheRequest($http_request, $user);

    $this->assertFalse($rule->isSatisfiedBy($request));
  }

  function testRequestNonmatch()
  {
    $rule = new lmbFullPageCacheRequestRule('!');

    $http_request = new lmbHttpRequest('http://dot.com?req1=1', array(), array());
    $user = new lmbFullPageCacheUser();
    $request = new lmbFullPageCacheRequest($http_request, $user);

    $this->assertFalse($rule->isSatisfiedBy($request));
  }

  function testRequestAttributesMatch()
  {
    $rule = new lmbFullPageCacheRequestRule(array('req1' => '1', 'req2' => '2'));

    $http_request = new lmbHttpRequest('http://dot.com?req1=1&req2=2', array(), array());
    $user = new lmbFullPageCacheUser();
    $request = new lmbFullPageCacheRequest($http_request, $user);

    $this->assertTrue($rule->isSatisfiedBy($request));
  }

  function testRequestAttributeGlobMatch()
  {
    $rule = new lmbFullPageCacheRequestRule(array('req1' => '*'));

    $http_request = new lmbHttpRequest('http://dot.com?req1=1', array(), array());
    $user = new lmbFullPageCacheUser();
    $request = new lmbFullPageCacheRequest($http_request, $user);

    $this->assertTrue($rule->isSatisfiedBy($request));
  }

  function testRequestAttributeNonmatch()
  {
    $rule = new lmbFullPageCacheRequestRule(array('req2' => 'b'));

    $http_request = new lmbHttpRequest('http://dot.com?req2=a', array(), array());
    $user = new lmbFullPageCacheUser();
    $request = new lmbFullPageCacheRequest($http_request, $user);

    $this->assertFalse($rule->isSatisfiedBy($request));
  }

  function testGetGlobMatch()
  {
    $rule = new lmbFullPageCacheRequestRule('*', '*');

    $http_request = new lmbHttpRequest('http://dot.com?req1=1', array(), array());
    $user = new lmbFullPageCacheUser();
    $request = new lmbFullPageCacheRequest($http_request, $user);

    $this->assertTrue($rule->isSatisfiedBy($request));
  }

  function testGetGlobNonmatch()
  {
    $rule = new lmbFullPageCacheRequestRule('*', '*');

    $http_request = new lmbHttpRequest('http://dot.com', array(), array());
    $user = new lmbFullPageCacheUser();
    $request = new lmbFullPageCacheRequest($http_request, $user);

    $this->assertFalse($rule->isSatisfiedBy($request));
  }

  function testGetNonmatch()
  {
    $rule = new lmbFullPageCacheRequestRule('*', '!');

    $http_request = new lmbHttpRequest('http://dot.com?req1=1', array(), array());
    $user = new lmbFullPageCacheUser();
    $request = new lmbFullPageCacheRequest($http_request, $user);

    $this->assertFalse($rule->isSatisfiedBy($request));
  }

  function testGetAttributesMatch()
  {
    $rule = new lmbFullPageCacheRequestRule('*', array('req1' => '1', 'req2' => '2'));

    $http_request = new lmbHttpRequest('http://dot.com?req1=1&req2=2', array(), array());
    $user = new lmbFullPageCacheUser();
    $request = new lmbFullPageCacheRequest($http_request, $user);

    $this->assertTrue($rule->isSatisfiedBy($request));
  }

  function testGetAttributeGlobMatch()
  {
    $rule = new lmbFullPageCacheRequestRule('*', array('req1' => '*'));

    $http_request = new lmbHttpRequest('http://dot.com?req1=1', array(), array());
    $user = new lmbFullPageCacheUser();
    $request = new lmbFullPageCacheRequest($http_request, $user);

    $this->assertTrue($rule->isSatisfiedBy($request));
  }

  function testGetAttributeNonmatch()
  {
    $rule = new lmbFullPageCacheRequestRule('*', array('req2' => 'b'));

    $http_request = new lmbHttpRequest('http://dot.com?req2=a', array(), array());
    $user = new lmbFullPageCacheUser();
    $request = new lmbFullPageCacheRequest($http_request, $user);

    $this->assertFalse($rule->isSatisfiedBy($request));
  }

  function testPostGlobMatch()
  {
    $rule = new lmbFullPageCacheRequestRule(null, null, '*');

    $http_request = new lmbHttpRequest('http://dot.com', array(), array('req1' => 1));
    $user = new lmbFullPageCacheUser();
    $request = new lmbFullPageCacheRequest($http_request, $user);

    $this->assertTrue($rule->isSatisfiedBy($request));
  }

  function testPostGlobNonmatch()
  {
    $rule = new lmbFullPageCacheRequestRule(null, null, '*');

    $http_request = new lmbHttpRequest('http://dot.com', array(), array());
    $user = new lmbFullPageCacheUser();
    $request = new lmbFullPageCacheRequest($http_request, $user);

    $this->assertFalse($rule->isSatisfiedBy($request));
  }

  function testPostNonmatch()
  {
    $rule = new lmbFullPageCacheRequestRule('*', '*', '!');

    $http_request = new lmbHttpRequest('http://dot.com', array(), array('req1' => 1));
    $user = new lmbFullPageCacheUser();
    $request = new lmbFullPageCacheRequest($http_request, $user);

    $this->assertFalse($rule->isSatisfiedBy($request));
  }

  function testPostAttributesMatch()
  {
    $rule = new lmbFullPageCacheRequestRule(null, null, array('req1' => '1', 'req2' => '2'));

    $http_request = new lmbHttpRequest('http://dot.com', array(), array('req1' => 1, 'req2' => 2));
    $user = new lmbFullPageCacheUser();
    $request = new lmbFullPageCacheRequest($http_request, $user);

    $this->assertTrue($rule->isSatisfiedBy($request));
  }

  function testPostAttributeGlobMatch()
  {
    $rule = new lmbFullPageCacheRequestRule(null, null, array('req1' => '*'));

    $http_request = new lmbHttpRequest('http://dot.com', array(), array('req1' => 1));
    $user = new lmbFullPageCacheUser();
    $request = new lmbFullPageCacheRequest($http_request, $user);

    $this->assertTrue($rule->isSatisfiedBy($request));
  }

  function testPostAttributeNonmatch()
  {
    $rule = new lmbFullPageCacheRequestRule('*', '*', array('req2' => 'b'));

    $http_request = new lmbHttpRequest('http://dot.com', array(), array('req2' => 'a'));
    $user = new lmbFullPageCacheUser();
    $request = new lmbFullPageCacheRequest($http_request, $user);

    $this->assertFalse($rule->isSatisfiedBy($request));
  }
}


