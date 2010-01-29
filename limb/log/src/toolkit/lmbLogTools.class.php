<?php

/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/toolkit/src/lmbAbstractTools.class.php');
lmb_require('limb/log/src/lmbLog.class.php');

/**
 * class lmbLogTools.
 *
 * @package log
 * @version $Id: lmbWebAppTools.class.php 8011 2009-12-25 08:51:27Z korchasa $
 */
class lmbLogTools extends lmbAbstractTools
{
  protected $log;

  function getLogDSNes()
  {
    $default_dsn = 'file://'.lmb_env_get('LIMB_VAR_DIR').'log/error.log';

    if(!$this->toolkit->hasConf('common'))
      return array($default_dsn);

    $conf = $this->toolkit->getConf('common');
    if(!isset($conf['logs']))
      return array($default_dsn);

    return $conf['logs'];
  }

  protected function _createLogWriter($dsn)
  {
    if(!is_object($dsn))
      $dsn = new lmbUri($dsn);

    $writer_name = 'lmbLog'.ucfirst($dsn->getProtocol()).'Writer';
    $writer_file = 'limb/log/src/'.$writer_name.'.class.php';
    try
    {
      lmb_require($writer_file);
      $writer = new $writer_name($dsn);
      return $writer;
    }
    catch(lmbFileNotFoundException $e)
    {
      throw new lmbFileNotFoundException($writer_file, 'Log writer not found');
    }
  }

  function getLog()
  {
    if($this->log)
      return $this->log;

    $this->log = new lmbLog();
    foreach($this->getLogDSNes() as $dsn)
      $this->log->registerWriter($this->_createLogWriter($dsn));

    return $this->log;
  }
}