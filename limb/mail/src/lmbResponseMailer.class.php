<?php

lmb_require('limb/mail/src/lmbBaseMailerInterface.interface.php');

class lmbResponseMailer implements lmbBaseMailerInterface
{  
  function sendHtmlMail($recipients, $sender, $subject, $html, $text = null, $charset = 'utf-8')
  {    
    $content = '<table cellspacing="5" cellpadding="8">';
    $content .= '<tr valign="top"><td bgcolor="#F8F8F8"><strong>Recipient:</strong></td><td><pre>'.print_r($recipients, true).'</pre></td></tr>';
    $content .= '<tr valign="top"><td bgcolor="#F8F8F8"><strong>Sender:</strong></td><td><pre>'.print_r($sender, true).'</pre></td></tr>';
    $content .= '<tr valign="top"><td bgcolor="#F8F8F8"><strong>Subject:</strong></td><td><pre>'.print_r($subject, true).'</pre></td></tr>';
    $content .= '<tr valign="top"><td bgcolor="#F8F8F8"><strong>HTML:</strong></td><td><pre>'.$html.'</pre></td></tr>';
    $content .= '<tr valign="top"><td bgcolor="#F8F8F8"><strong>Text:</strong></td><td><pre>'.$text.'</pre></td></tr>';
    $content .= '<tr valign="top"><td bgcolor="#F8F8F8"><strong>Charset:</strong></td><td><pre>'.$charset.'</pre></td></tr>';
    $content .= '</table>';
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