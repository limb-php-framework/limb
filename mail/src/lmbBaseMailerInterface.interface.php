<?php

interface lmbBaseMailerInterface
{
  function sendHtmlMail($recipients, $sender, $subject, $html, $text = null, $charset = 'utf-8');
  function sendPlainMail($recipients, $sender, $subject, $body, $charset = 'utf-8');
  function setConfig($config);
}