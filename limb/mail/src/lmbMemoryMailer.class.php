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

  static $mail_contents = array();

  function sendHtmlMail($recipients, $sender, $subject, $html, $text = null, $charset = 'utf-8')
  {
    $this->recipient = $recipients;
    $this->sender = $sender;
    $this->subject = $subject;
    $this->html = $html;
    $this->text = $text;
    $this->charset = $charset;

    self::$mail_contents[] = $this->html;
  }

  function sendPlainMail($recipients, $sender, $subject, $body, $charset = 'utf-8')
  {
    $this->recipient = $recipients;
    $this->sender = $sender;
    $this->subject = $subject;
    $this->text = $body;
    $this->charset = $charset;
  }

  static function getMailContents()
  {
    return self::$mail_contents;
  }

  static function clearMailContents()
  {
    self::$mail_contents = array();
  }
  
  function setConfig($config)  {}
}