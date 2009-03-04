<?php

lmb_require('limb/mail/src/lmbBaseMailerInterface.interface.php');

class lmbResponseMailer implements lmbBaseMailerInterface
{  
  function sendHtmlMail($recipients, $sender, $subject, $html, $text = null, $charset = 'utf-8')
  {    
    $content = '';
    $content .= '<p>recipient: '.$recipients.'</p>';
    $content .= '<p>sender: '.$sender.'</p>';
    $content .= '<p>subject: '.$subject.'</p>';
    $content .= '<pre>html: '.$html.'</pre>';
    $content .= '<pre>text: '.$text.'</pre>';
    $content .= '<p>charset: '.$charset.'</p>';
    lmbToolkit::instance()->getResponse()->write($content);
  }
  
  function sendPlainMail($recipients, $sender, $subject, $body, $charset = 'utf-8')
  { 
    $content = '';
    $content .= '<p>recipient: '.$recipients.'</p>';
    $content .= '<p>sender: '.$sender.'</p>';
    $content .= '<p>subject: '.$subject.'</p>';
    $content .= '<pre>text: '.$body.'</pre>';
    $content .= '<p>charset: '.$charset.'</p>';
    lmbToolkit::instance()->getResponse()->write($content);
  }  
}