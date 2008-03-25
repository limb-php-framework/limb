<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
@define('PHPMAILER_DIR', dirname(__FILE__) . '/../lib/phpmailer-1.73/');
@define('LIMB_USE_PHPMAIL', false);
@define('LIMB_SMTP_PORT', '25');
@define('LIMB_SMTP_HOST', 'localhost');
@define('LIMB_SMTP_AUTH', false);
@define('LIMB_SMTP_USER', '');
@define('LIMB_SMTP_PASSWORD', '');

/**
 * class lmbMailer.
 *
 * @package mail
 * @version $Id: lmbMailer.class.php 6856 2008-03-25 12:58:31Z korchasa $
 */
class lmbMailer
{
  protected $attachments = array();

  protected $_config_properties_map = array(
    'phpmailer_dir' => 'PHPMAILER_DIR',
    'use_phpmail' => 'LIMB_USE_PHPMAIL',
    'smtp_host' => 'LIMB_SMTP_HOST',
    'smtp_port' => 'LIMB_SMTP_PORT',
    'smtp_auth' => 'LIMB_SMTP_AUTH',
    'smtp_user' => 'LIMB_SMTP_USER',
    'smtp_password' => 'LIMB_SMTP_PASSWORD'
  );

  public $phpmailer_dir;
  public $use_phpmail;
  public $smtp_host;
  public $smtp_port;
  public $smtp_auth;
  public $smtp_user;
  public $smtp_password;

  function __construct($config = false)
  {
    $this->_setConfigFromDefinedContants();

    if($config)
      $this->setConfig($config);

  }

  protected function _setConfigFromDefinedContants()
  {
    foreach($this->_config_properties_map as $property_name => $define_name)
      $this->$property_name = constant($define_name);
  }

  public function setConfig($config = array())
  {
    foreach($config as $property_name => $property_value)
      $this->$property_name = $property_value;
  }

  protected function _createMailer()
  {
    include_once($this->phpmailer_dir . '/class.phpmailer.php');

    $mailer = new PHPMailer();
    $mailer->LE = "\r\n";

    if($this->use_phpmail)
      return $mailer;

    $mailer->IsSMTP();
    $mailer->Host = $this->smtp_host;
    $mailer->Port = $this->smtp_port;

    if($this->smtp_auth == true)
    {
      $mailer->SMTPAuth = true;
      $mailer->Username = $this->smtp_user;
      $mailer->Password = $this->smtp_password;
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
