<?php

lmb_require('limb/mail/src/lmbBaseMailerInterface.interface.php');

class lmbFirePHPMailer implements lmbBaseMailerInterface
{
  function sendHtmlMail($recipients, $sender, $subject, $html, $text = null, $charset = 'utf-8')
  {
    $mail = array(
      'recipients' => $recipients,
      'sender' => $sender,
      'subject' => $subject,
      'html' => $html,
      'text' => $text,
      'charset' => $charset,
    );
    $this->_send($mail);
  }

  function sendPlainMail($recipients, $sender, $subject, $body, $charset = 'utf-8')
  {
    $mail = array(
      'recipients' => $recipients,
      'sender' => $sender,
      'subject' => $subject,
      'subject' => $subject,
      'charset' => $charset,
    );
    $this->_send($mail);
  }

  function setConfig($config)  {}
  
  protected function _send($content)
  {
    if(!function_exists('fb'))
      throw new lmbException("FirePHP function 'fb' not found");
    fb($content, 'new mail');
  }
}