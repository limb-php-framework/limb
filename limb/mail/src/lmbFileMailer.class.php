<?php

lmb_require('limb/mail/src/lmbBaseMailerInterface.interface.php');

class lmbFileMailer implements lmbBaseMailerInterface
{
  function sendHtmlMail($recipients, $sender, $subject, $html, $text = null, $charset = 'utf-8')
  {
    $content =<<<EOD
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head><meta http-equiv='Content-Type' content='text/html; charset=utf-8' /></head>
<body>
EOD;
    $content .= '<p>recipient: '.$recipients.'</p>';
    $content .= '<p>sender: '.$sender.'</p>';
    $content .= '<p>subject: '.$subject.'</p>';
    $content .= '<pre>html: '.$html.'</pre>';
    $content .= '<pre>text: '.$text.'</pre>';
    $content .= '<p>charset: '.$charset.'</p>';

    $content .= '</body></html>';

    $this->_send($recipients,$content);
  }

  function sendPlainMail($recipients, $sender, $subject, $body, $charset = 'utf-8')
  {
    $content = '';
    $content .= '<p>recipient: '.$recipients.'</p>';
    $content .= '<p>sender: '.$sender.'</p>';
    $content .= '<p>subject: '.$subject.'</p>';
    $content .= '<pre>text: '.$body.'</pre>';
    $content .= '<p>charset: '.$charset.'</p>';

    $this->_send($recipients,$content);
  }

  function setConfig($config)  {}
    
  protected function _send($recipients, $content)
  {
    lmbFs::safeWrite(lmb_env_get('LIMB_VAR_DIR').'/mail/'.(pow(2,31) - time()).'_'.$recipients.'_'.microtime(true).'.txt', $content);
  }
}