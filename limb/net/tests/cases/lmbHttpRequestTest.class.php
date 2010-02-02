<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/net/src/lmbHttpRequest.class.php');
lmb_require('limb/net/src/lmbUri.class.php');

class lmbHttpRequestTest extends UnitTestCase
{
  function testGetUri()
  {
    $request = new lmbHttpRequest('http://test.com');
    $this->assertEqual($request->getUri(), new lmbUri('http://test.com'));
  }

  function testGetUriPath()
  {
    $request = new lmbHttpRequest('http://test.com/path?foo=1');
    $this->assertEqual($request->getUriPath(), '/path');
  }

  function testGet()
  {
    $request = new lmbHttpRequest('http://test.com', array('c' => 1), array('d' => 2));
    $this->assertEqual($request->get('c'), 1);
    $this->assertEqual($request->get('d'), 2);
    $this->assertNull($request->get('foo'));
  }

  function testMergePostOverGet()
  {
    $request = new lmbHttpRequest('http://test.com', array('a' => 2), array('a' => 3));
    $this->assertEqual($request->get('a'), 3);
  }

  function testGetSafe()
  {
    $request = new lmbHttpRequest('http://test.com', array('c' => '<xss>'));
    $this->assertEqual($request->getSafe('c'), htmlspecialchars('<xss>'));
  }

  function testGetRequest()
  {
    $request = new lmbHttpRequest('http://test.com', array('c' => 1), array('d' => 2));
    $this->assertEqual($request->getRequest(), array('c' => 1, 'd' => 2));
    $this->assertEqual($request->getRequest('c'), 1);
    $this->assertNull($request->getRequest('b'), 1);

    $this->assertEqual($request->getRequest('b', 1), 1); // test for default values
    $this->assertIdentical($request->getRequest('b', 0), 0);

    $this->assertEqual($request->getRequest(array('b', 'c', 'd')), array('b' => null, 'c' => 1, 'd' => 2));
  }

  function testGetGet()
  {
  	$get = array('c' => 1, 'ju' => 'jitsu', 'kung' => 'fu');
    $request = new lmbHttpRequest('http://test.com', $get);
    $this->assertEqual($request->getGet(), $get);
    $this->assertEqual($request->getGet('c'), 1);
    $this->assertNull($request->getGet('b'), 1);

    $this->assertEqual($request->getGet('sambo', 'cool'), 'cool'); // test for default values
    $this->assertIdentical($request->getGet('sambo', 0), 0);

    $field_names = array('ju', 'kung', 'sambo');

    $this->assertEqual($request->getGet($field_names), array('ju' => 'jitsu', 'kung' => 'fu', 'sambo' => null));
  }

  function testGetPost()
  {
  	$post = array('c' => 1, 'ju' => 'jitsu', 'kung' => 'fu');
    $request = new lmbHttpRequest('http://test.com', array(), $post);
    $this->assertEqual($request->getPost(), $post);
    $this->assertEqual($request->getPost('c'), 1);
    $this->assertNull($request->getPost('b'), 1);

    $this->assertEqual($request->getPost('sambo', 'cool'), 'cool'); // test for default values
    $this->assertIdentical($request->getPost('sambo', 0), 0);

    $field_names = array('ju', 'kung', 'sambo');

    $this->assertEqual($request->getPost($field_names), array('ju' => 'jitsu', 'kung' => 'fu', 'sambo' => null));
  }

  function testGetFiltered()
  {
    $request = new lmbHttpRequest('http://test.com', array('c' => 'c1'));
    $this->assertEqual($request->getFiltered('c', FILTER_SANITIZE_NUMBER_INT), 1);
    $this->assertEqual($request->getFiltered('d', FILTER_SANITIZE_NUMBER_INT, 1), 1);
  }

  function testGetGetFiltered()
  {
    $request = new lmbHttpRequest('http://test.com', array('c' => 'c1'));
    $this->assertEqual($request->getGetFiltered('c', FILTER_SANITIZE_NUMBER_INT), 1);
    $this->assertEqual($request->getGetFiltered('d', FILTER_SANITIZE_NUMBER_INT, 1), 1);
  }

  function testGetGetFiltered_Array()
  {
    $request = new lmbHttpRequest('http://test.com', array('c' => 'c1', 'ju' => 'jitsu42'));
    $vars = $request->getGetFiltered(
        array('c', 'ju'),
        array('c' => FILTER_SANITIZE_NUMBER_INT, 'ju' => FILTER_SANITIZE_NUMBER_INT)
    );
    $this->assertEqual($vars['c'], 1);
    $this->assertEqual($vars['ju'], 42);
  }

  function testGetPostFiltered()
  {
    $post = array('c' => 'c1');
    $request = new lmbHttpRequest('http://test.com', array(), $post);
    $this->assertEqual($request->getPostFiltered('c', FILTER_SANITIZE_NUMBER_INT), 1);
    $this->assertEqual($request->getPostFiltered('d', FILTER_SANITIZE_NUMBER_INT, 1), 1);
  }

  function testGetPostFiltered_Array()
  {
    $post = array('c' => 'c1', 'ju' => 'jitsu42');
    $request = new lmbHttpRequest('http://test.com', array(), $post);
    $vars = $request->getPostFiltered(array('c', 'ju'), FILTER_SANITIZE_NUMBER_INT);
    $this->assertEqual($vars['c'], 1);
    $this->assertEqual($vars['ju'], 42);
  }

  function testGetCookie()
  {
  	$cookie = array('c' => 1, 'ju' => 'jitsu', 'kung' => 'fu');
    $request = new lmbHttpRequest('http://test.com', array(), array(), $cookie);
    $this->assertEqual($request->getCookie(), $cookie);
    $this->assertEqual($request->getCookie('c'), 1);
    $this->assertNull($request->getCookie('b'), 1);

    $this->assertEqual($request->getCookie('sambo', 'cool'), 'cool'); // test for default values
    $this->assertIdentical($request->getCookie('sambo', 0), 0);

    $field_names = array('ju', 'kung', 'sambo');

    $this->assertEqual($request->getCookie($field_names), array('ju' => 'jitsu', 'kung' => 'fu', 'sambo' => null));
  }

  function testGetFiles()
  {
     $files = array(
        'form' => array(
           'name' => array(
                           'file1' => 'file',
                           'file2' => 'file',
                           ),
           'type' => array(
                           'file1' => 'file_type',
                           'file2' => 'file_type',
                           ),
           'tmp_name' => array(
                               'file1' => 'file_tmp_name',
                               'file2' => 'file_tmp_name',
                               ),
           'size' => array(
                           'file1' => 'file_size',
                           'file2' => 'file_size',
                           ),
           'error' => array(
                           'file1' => 'file_err_code',
                           'file2' => 'file_err_code',
                           ),
         ),
     );

     $expected = array(
        'form' => array(
          'file1' => new lmbUploadedFile(array(
             'name' => 'file',
             'type' => 'file_type',
             'tmp_name' => 'file_tmp_name',
             'size' => 'file_size',
             'error' => 'file_err_code'
           )),
          'file2' => new lmbUploadedFile(array(
             'name' => 'file',
             'type' => 'file_type',
             'tmp_name' => 'file_tmp_name',
             'size' => 'file_size',
             'error' => 'file_err_code'
           )),
         ),
     );

    $request = new lmbHttpRequest('http://test.com', array(), array(), array(), $files);
    $this->assertEqual($request->getFiles(), $expected);
    $this->assertEqual($request->getFiles('form'), $expected['form']);

    //files ARE returned with raw get
    $this->assertEqual($request->get('form'), $expected['form']);
  }

  function testHasFiles()
  {
    $files = array(
      'form' => array(
        'name' => array('file1' => 'file'),
        'type' => array('file1' => 'file_type'),
        'tmp_name' => array('file1' => 'file_tmp_name'),
        'size' => array('file1' => 'file_size'),
        'error' => array('file1' => 'file_err_code'),
      ),
    );

    $request = new lmbHttpRequest('http://test.com', array(), array(), array(), $files);
    $this->assertEqual($request->hasFiles(), true);
    $this->assertEqual($request->hasFiles('form'), true);
    $this->assertEqual($request->hasFiles('not_existed_form'), false);
  }

  function testInitByServerVariables()
  {
    $old_uri = @$_SERVER['REQUEST_URI'];
    $old_host = @$_SERVER['HTTP_HOST'];
    $old_port = @$_SERVER['SERVER_PORT'];

    $_SERVER['REQUEST_URI'] = '/';
    $_SERVER['HTTP_HOST'] = 'test.com';
    $_SERVER['SERVER_PORT'] = '8080';

    $request = new lmbHttpRequest();
    $this->assertEqual($request->getRawUriString(), 'http://test.com:8080/');

    $_SERVER['REQUEST_URI'] = $old_uri;
    $_SERVER['HTTP_HOST'] = $old_host;
    $_SERVER['SERVER_PORT'] = $old_port;
  }

  function testExtractPortFromHost()
  {
    $old_uri = @$_SERVER['REQUEST_URI'];
    $old_host = @$_SERVER['HTTP_HOST'];

    $_SERVER['REQUEST_URI'] = '/';
    $_SERVER['HTTP_HOST'] = 'test.com:8787';

    $request = new lmbHttpRequest();
    $this->assertEqual($request->getRawUriString(), 'http://test.com:8787/');

    $_SERVER['REQUEST_URI'] = $old_uri;
    $_SERVER['HTTP_HOST'] = $old_host;
  }

  function testToString()
  {
     $files = array(
        'file1' => array(
                         'name' => 'file',
                         'type' => 'file_type',
                         'tmp_name' => 'file_tmp_name',
                         'size' => 'file_size',
                         'error' => 'file_err_code'
                         )
     );

    $request = new lmbHttpRequest('http://test.com?z=1',
                                array('b' => array('c' => 1)),
                                array('d' => 2),
                                //only request data(post, get) should be present in result string
                                array('cookie' => 2),
                                $files);
    $this->assertEqual($request->toString(), 'http://test.com?b[c]=1&z=1&d=2');
  }

  function testUriQueryOverridesGets()
  {
    $request = new lmbHttpRequest('http://test.com?a=1', array('a' => 2), array());

    $this->assertEqual($request->get('a'), 1);
  }

  function testToString_ValidForConstruct_LmbHttpRequest_IfAttributeNoValidStringURL()
  {
    $request = new lmbHttpRequest('http://test.com?z=1',
                                array('b' => array('c' => '&m=7')),
                                array('d' => '?&n=9#top'));
    $request = new lmbHttpRequest($request->toString());

    $this->assertEqual($request->get('z'), 1);
    $this->assertEqual($request->get('b'), array('c' => '&m=7'));
    $this->assertEqual($request->get('d'), '?&n=9#top');
    $this->assertEqual($request->getUri()->getAnchor(), '');

    $this->assertNull($request->get('m'));
    $this->assertNull($request->get('n'));
  }

  function testForNotSetReservedParams()
  {
    $request = new lmbHttpRequest('http://test.com?__request=1');
    $this->assertNull($request->get('__request'));
    $this->assertEqual('1', $request->getGet('__request'));
  }

  function testArrayAccess()
  {
    $request = new lmbHttpRequest('http://test.com/wow?z=1');
    $this->assertEqual($request['uri']['path'], '/wow');
    $this->assertEqual($request['get']['z'], '1');
  }
}


