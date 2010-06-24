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
lmb_require('limb/net/src/lmbUri.class.php');

/**
 * class lmbLogTools.
 *
 * @package log
 * @version $Id: lmbWebAppTools.class.php 8011 2009-12-25 08:51:27Z korchasa $
 */
class lmbLogTools extends lmbAbstractTools
{
  protected $_logs = array();

  function setLog($log, $name = 'default')
  {
    $this->_logs[$name] = $log;
  }

  function getLog($name = 'default')
  {
    if(!isset($this->_logs[$name]))
    {
      $this->_logs[$name] = $this->toolkit->getLogFromConf($name);
    }

    return $this->_logs[$name];
  }

  /**
   * @param string $name
   * @return lmbLog
   */
  function getLogFromConf($name)
  {
    lmb_assert_true($name);
    lmb_assert_type($name, 'string');
    if($this->toolkit->hasConf('log'))
    {
        $conf = $this->toolkit->getConf('log');
        $log = $this->toolkit->createLog($conf['logs'][$name]);
    }
    else
        $log = $this->toolkit->getDefaultLog();
    return $log;
  }

  /**
   * @param array[string|lmbUri|lmbLogWriter] $dsnes_or_writers
   * @return lmbLog
   */
  function createLog($dsnes_or_writers)
  {
    if(!is_array($dsnes_or_writers))
      $dsnes_or_writers = array($dsnes_or_writers);

    $log = $this->_createLogObject();
    foreach ($dsnes_or_writers as $dsn_or_writer)
    {
      if(is_object($dsn_or_writer) && $dsn_or_writer instanceof lmbLogWriter)
        $writer = $dsn_or_writer;
      else
        $writer = $this->toolkit->createLogWriterByDSN($dsn_or_writer);

      $log->registerWriter($writer);
    }
    return $log;
  }

  /**
   * @param string|lmbUri $dsn
   * @return lmbLogWriter
   */
  function createLogWriterByDSN($dsn)
  {
    if(!is_object($dsn))
      $dsn = new lmbUri($dsn);

    if(!$dsn->getProtocol())
      throw new lmbException('Empty log writer type', array('dsn' => $dsn->toString()));

    $writer_name = 'lmbLog'.ucfirst($dsn->getProtocol()).'Writer';
    $writer_file = 'limb/log/src/writers/'.$writer_name.'.class.php';
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

  /**
   * @param string $name
   * @return lmbLogWriter
   */
  function createLogWritersByName($name)
  {
    if(!$this->toolkit->hasConf('log') && 'default' === $name)
      return $this->_createDefaultWriters();

    $conf = $this->toolkit->getConf('log');
    $dsnes = $conf['logs'][$name];

    if(!is_array($dsnes))
      $dsnes = array($dsnes);

    $writers = array();
    foreach ($dsnes as $key => $dsn)
    {
      if(!is_string($dsn) && !(is_object($dsn) && ($dsn instanceof lmbUri)))
        throw new lmbInvalidArgumentException('DSN must be a string or lmbUri instance', array('given' => $dsn));
      $writers[$key] = $this->toolkit->createLogWriterByDSN($dsn);
    }
    return $writers;
  }

  function getDefaultLog()
  {
    return $this->createLog('file://'.lmb_var_dir().'/logs/error.log');
  }

  protected function _createLogObject()
  {
    return new lmbLog();
  }
}