<?php

lmb_require('limb/mail/src/lmbBaseMailerInterface.interface.php');

class lmbMemoryMailer implements lmbBaseMailerInterface
{
  var $recipient;
  var $sender;
  var $subject;
  var $html;
  var $text;
  var $charset;
  
  function sendHtmlMail($recipients, $sender, $subject, $html, $text = null, $charset = 'utf-8')
  {    
    $this->recipient = $recipients;
    $this->sender = $sender;
    $this->subject = $subject;
    $this->html = $html;
    $this->text = $text;
    $this->charset = $charset;
  }
  
  function sendPlainMail($recipients, $sender, $subject, $body, $charset = 'utf-8')
  {
    $this->recipient = $recipients;
    $this->sender = $sender;
    $this->subject = $subject;
    $this->text = $body;
    $this->charset = $charset;
  }  
}