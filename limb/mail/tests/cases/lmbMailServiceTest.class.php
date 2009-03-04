<?php

lmb_require('limb/macro/tests/cases/lmbBaseMacroTest.class.php');
lmb_require('limb/mail/src/lmbMailService.class.php');

class lmbMailServiceTest extends lmbBaseMacroTest
{
  function setUp()
  {
    parent::setUp();
    lmbFs::mkdir($this->tpl_dir . '/_mail');
    lmbToolkit::instance()->setConf('macro', $this->_createMacroConfig());
  }
  
  function tearDown(){}  
   
  function testGetMailHtmlContent()
  {          
  	$mail_template = <<<EOD
subj

{\$#foo}bar
EOD;
  	
    $this->_createTemplate($mail_template, '_mail/baz.phtml');    
    $service = new lmbMailService('baz');
    $service->set('foo', 42);
    
    $this->assertEqual('subj', $service->getSubject());
    $this->assertEqual('42bar', $service->getHtmlContent());
  }
  
  function testGetMailTextContent()
  {          
    $mail_template = <<<EOD
subj

{\$#bar}foo
EOD;
    
    $this->_createTemplate($mail_template, '_mail/baz.phtml');
    
    $service = new lmbMailService('baz');
    $service->set('bar', 11);
    
    $this->assertEqual('subj', $service->getSubject());
    $this->assertEqual('11foo', $service->getTextContent());
  }
  
  function testGetMailBothContents()
  {          
  	$mail_template = <<<EOD
subj

{\$#text_dynamic}text_static

{\$#html_dynamic}html_static
EOD;
    
    $this->_createTemplate($mail_template, '_mail/baz.phtml');
    
    $service = new lmbMailService('baz');
    $service->set('text_dynamic', 42);
    $service->set('html_dynamic', 11);
    
    $this->assertEqual('subj', $service->getSubject());
    $this->assertEqual('42text_static', $service->getTextContent());
    $this->assertEqual('11html_static', $service->getHtmlContent());
  }
}