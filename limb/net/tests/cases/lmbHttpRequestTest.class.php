<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
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
  }

  function testGetGet()
  {
    $request = new lmbHttpRequest('http://test.com', array('c' => 1));
    $this->assertEqual($request->getGet(), array('c' => 1));
    $this->assertEqual($request->getGet('c'), 1);
    $this->assertNull($request->getGet('b'), 1);
  }

  function testGetPost()
  {
    $request = new lmbHttpRequest('http://test.com', array(), array('c' => 1));
    $this->assertEqual($request->getPost(), array('c' => 1));
    $this->assertEqual($request->getPost('c'), 1);
    $this->assertNull($request->getPost('b'), 1);
  }

  function testGetCookie()
  {
    $request = new lmbHttpRequest('http://test.com', array(), array(), array('c' => 1));
    $this->assertEqual($request->getCookie(), array('c' => 1));
    $this->assertEqual($request->getCookie('c'), 1);
    $this->assertNull($request->getCookie('b'), 1);
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

  function testGetFilesMultipartException()
  {
    $old = @$_SERVER['CONTENT_TYPE'];

    $_SERVER['CONTENT_TYPE'] = 'blah';

    $request = new lmbHttpRequest('http://test.com');
    $request->getFiles();//it's ok, no post request

    $request->pretendPost();

    try
    {
      $request->getFiles();
      $this->assertTrue(false);
    }
    catch(lmbException $e){}

    $_SERVER['CONTENT_TYPE'] = $old;
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
}


