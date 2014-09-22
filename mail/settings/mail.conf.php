<?php

$conf = array(
  'phpmailer_version_name' => lmb_env_get('PHPMAILER_VERSION_NAME', 'phpmailer-5.2.8'),
  'use_phpmail'            => lmb_env_get('LIMB_USE_PHPMAIL', false),
  'smtp_host'              => lmb_env_get('LIMB_SMTP_HOST', 'localhost'),
  'smtp_port'              => lmb_env_get('LIMB_SMTP_PORT', '25'),
  'smtp_auth'              => lmb_env_get('LIMB_SMTP_AUTH', false),
  'smtp_user'              => lmb_env_get('LIMB_SMTP_USER', ''),
  'smtp_password'          => lmb_env_get('LIMB_SMTP_PASSWORD', ''),
  'smtp_secure'            => lmb_env_get('LIMB_SMTP_SECURE', ''),  /* Options: "", "ssl" or "tls" */
  'sender'                 => 'set-me-in-mail-conf@limb-project.com',
  'macro_template_parser'  => 'mailpart'
);
