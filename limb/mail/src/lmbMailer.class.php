<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbMailer.class.php 5000 2007-02-08 15:36:41Z pachanga $
 * @package    mail
 */
@define('PHPMAILER_DIR', dirname(__FILE__) . '/../lib/phpmailer-1.72/');
@define('LIMB_USE_PHPMAIL', false);
@define('LIMB_SMTP_PORT', '25');
@define('LIMB_SMTP_HOST', 'localhost');
@define('LIMB_SMTP_AUTH', false);
@define('LIMB_SMTP_USER', '');
@define('LIMB_SMTP_PASSWORD', '');

class lmbMailer
{
  protected $attachments = array();

  protected function _createMailer()
  {
    include_once(PHPMAILER_DIR . '/class.phpmailer.php');

    $mailer = new PHPMailer();
    $mailer->LE = "\r\n";

    if(LIMB_USE_PHPMAIL)
      return $mailer;

    $mailer->IsSMTP();
    $mailer->Host = LIMB_SMTP_HOST;
    $mailer->Port = LIMB_SMTP_PORT;

    if(LIMB_SMTP_AUTH == true)
    {
      $mailer->SMTPAuth = true;
      $mailer->Username = LIMB_SMTP_USER;
      $mailer->Password = LIMB_SMTP_PASSWORD;
    }
    return $mailer;
  }

  function addAttachment($path, $name = "", $encoding = "base64", $type = "application/octet-stream")
  {
    $this->attachments[] = array(
      'path' => $path,
      'name' => $name,
      'encoding' => $encoding,
      'type' => $type
    );
  }

  function sendPlainMail($recipients, $sender, $subject, $body, $charset = 'utf-8')
  {
    $mailer = $this->_createMailer();

    $mailer->IsHTML(false);
    $mailer->CharSet = $charset;

    if(!empty($this->attachments))
      $this->_addAttachments($mailer);

    $recipients = $this->processMailRecipients($recipients);

    foreach($recipients as $recipient)
      $mailer->AddAddress($recipient['address'], $recipient['name']);

    if(!$sender = $this->processMailAddressee($sender))
      return false;

    $mailer->From = $sender['address'];
    $mailer->FromName = $sender['name'];
    $mailer->Subject = $subject;
    $mailer->Body    = $body;

    return $mailer->Send();
  }

  function sendHtmlMail($recipients, $sender, $subject, $html, $text = null, $charset = 'utf-8')
  {
    $mailer = $this->_createMailer();

    $mailer->IsHTML(true);
    $mailer->CharSet = $charset;

    $mailer->Body = $html;

    if(!empty($this->attachments))
      $this->_addAttachments($mailer);

    if(!is_null($text))
      $mailer->AltBody = $text;

    $recipients = $this->processMailRecipients($recipients);

    foreach($recipients as $recipient)
      $mailer->AddAddress($recipient['address'], $recipient['name']);

    if(!$sender = $this->processMailAddressee($sender))
      return false;

    $mailer->From = $sender['address'];
    $mailer->FromName = $sender['name'];
    $mailer->Subject = $subject;

    return $mailer->Send();
  }

  function processMailRecipients($recipients)
  {
    if(!is_array($recipients))
       $recipients = array($recipients);
    $result = array();
    foreach($recipients as $recipient)
    {
      if($recipient = $this->processMailAddressee($recipient))
        $result[] = $recipient;
    }

    return $result;
  }

  function processMailAddressee($adressee)
  {
    if(is_array($adressee))
    {
      if(isset($adressee['address']) && isset($adressee['name']))
        return $adressee;

      return null;
    }
    elseif(preg_match('~("|\')?([^"\']+)("|\')?\s+<([^>]+)>~', $adressee, $matches))
      return array('address' => $matches[4], 'name' => $matches[2]);
    else
      return array('address' => $adressee, 'name' => '');
  }

  protected function _addAttachments($mailer)
  {
    foreach($this->attachments as $attachment)
      $mailer->AddAttachment($attachment['path'],
                             $attachment['name'],
                             $attachment['encoding'],
                             $attachment['type']);
  }
}

?>