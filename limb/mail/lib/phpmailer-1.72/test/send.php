<?php
require("../class.phpmailer.php");

$mail = new PHPMailer();

//$mail->IsSMTP();                                      // set mailer to use SMTP
//$mail->Host = "office.bit";  // specify main and backup server
$mail->Host = "192.168.0.1";  // specify main and backup server
//$mail->SMTPAuth = true;     // turn on SMTP authentication
//$mail->Username = "jswan";  // SMTP username
//$mail->Password = "secret"; // SMTP password

$mail->From = "pachanga@office.bit";
$mail->FromName = "Pacha";
//$mail->AddAddress("pachanga@office.bit", "Mike");
$mail->AddAddress("mike@office.bit", "Mike");
//$mail->AddAddress("dbrain@office.bit", "Dbrain");                  // name is optional
$mail->AddReplyTo("info@example.com", "Information");

$mail->WordWrap = 50;                                 // set word wrap to 50 characters
$mail->AddAttachment("C:\\My Shared Folder\\christina aguilera - bra, panties, stockings handcuffs.jpg", "aguillera.jpg");    // optional name
$mail->AddAttachment("DCC.doc", "dcc.doc");    // optional name
$mail->AddEmbeddedImage("C:\\My Shared Folder\\christina aguilera - bra, panties, stockings handcuffs.jpg",
                        "my-attach",
                        "aguillera.jpg",
                        "base64",
                        "image/jpg");
$mail->IsHTML(true);                                  // set email format to HTML

$mail->Subject = "New Mailer";
$mail->Body    = "This is the HTML message body <img alt=\"aguilera\" src=\"cid:my-attach\"> <b>in bold!</b>";
$mail->AltBody = "This is the HTML\nmessage body\nin bold!";

if(!$mail->Send())
{
   echo "Message could not be sent. <p>";
   echo "Mailer Error: " . $mail->ErrorInfo;
   exit;
}

echo "Message has been sent";
?>
