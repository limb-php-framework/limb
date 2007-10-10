  {
  function testOpen()
  {
    $uri = new MockUri();
    $reader = new lmbUriContentReader();
    $uri->expectOnce('toString');
    $uri->setReturnValue('toString', dirname(__FILE__) . '/../html/index.html');
    $reader->open($uri);
    $this->assertFalse($reader->getContentType()); // since opening a plain text file not html over http
    $this->assertEqual($reader->getContent(),
                       file_get_contents(dirname(__FILE__) . '/../html/index.html'));
  }

  function testFilterAcceptedProtocols()
  {
    $links = array(new lmbUri('http://test1.com'),
                   new lmbUri('svn+ssh://test-broken.com'),
                   new lmbUri('https://test1.com'),
                   new lmbUri('ftp://test-broken.com'));

    $this->filter->allowHost('test1.com');
    $this->filter->allowPathRegex('~.*~');
    $this->filter->allowProtocol('http');
    $this->filter->allowProtocol('HTTPS');//protocols are lowercased

    $this->assertTrue($this->filter->canPass($links[0]));
    $this->assertFalse($this->filter->canPass($links[1]));
    $this->assertTrue($this->filter->canPass($links[2]));
    $this->assertFalse($this->filter->canPass($links[3]));
  }

  function testFilterAcceptedHosts()
  {
    $links = array(new lmbUri('http://www.test1.com/some/path'),
                   new lmbUri('http://test-broken.com'),
                   new lmbUri('http://test1.com'),
                   new lmbUri('http://microsoft.com'));

    $this->filter->allowProtocol('http');
    $this->filter->allowPathRegex('~.*~');
    $this->filter->allowHost('test1.com');
    $this->filter->allowHost('www.TEST1.com');//hosts are lowercased

    $this->assertTrue($this->filter->canPass($links[0]));
    $this->assertFalse($this->filter->canPass($links[1]));
    $this->assertTrue($this->filter->canPass($links[2]));
    $this->assertFalse($this->filter->canPass($links[3]));
  }
 }

