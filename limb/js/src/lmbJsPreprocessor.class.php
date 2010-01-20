<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/toolkit/src/lmbToolkit.class.php');
lmb_require('limb/fs/src/lmbFs.class.php');
lmb_require('limb/js/src/lmbJsDirectiveHandlers.class.php');

lmb_env_setor('LIMB_JS_INCLUDE_PATH', 'www/js;limb/*/shared/js');

/**
 * class lmbJsPreprocessor.
 *
 * @package js
 * @version $Id$
 */
class lmbJsPreprocessor
{
  protected $processed = array();
  protected $directives = array();
  protected $error = false;

  function __construct()
  {
    $this->toolkit = lmbToolkit :: instance();
    $this->addDirective('include', array(&$this, '_processInclude'));
  }

  function addDirective($directive, $handler)
  {
    $this->handlers[$directive] = $handler;
  }

  function processFiles($files)
  {
    $contents = '';

    foreach($files as $file)
    {
      if(!$processed_file_contents = $this->processFile($file))
        continue;

      $contents .= $processed_file_contents . "\n";
    }

    return $contents;
  }

  function processFile($file)
  {
    $file = lmbFs :: normalizePath($file);

     if($this->_isProcessed($file))
      return '';

    $contents = file_get_contents($file);
    $this->_markAsProcessed($file);

    $result = $this->_processDirectives($contents);

    return $result;
  }

  protected function _processDirectives(&$contents)
  {
    $processed_contents = preg_replace_callback('~^//#([a-z_\\-0-9]+)\s+(.*)$~m',
                                                array(&$this, '_processDirective'),
                                                $contents);

    // repeatedly throw saved exception to prevent preg_replace_callback warning
    if($this->error)
      throw $this->error;

    return $processed_contents;
  }

  protected function _processDirective($matches)
  {
    $params = $this->_parseDirectiveParams($matches[2]);

    if(!isset($this->handlers[$matches[1]]))
      return '';

    return call_user_func_array($this->handlers[$matches[1]], $params);
  }

  protected function _parseDirectiveParams($params_string)
  {
    if(!$params_string)
      return array();

    $params = explode(' ', $params_string);
    foreach($params as $key => $param)
    {
      if(!$param)
        unset($params[$key]);
    }

    return $params;
  }

  protected function _markAsProcessed($file)
  {
    $this->processed[$file] = 1;
  }

  protected function _isProcessed($file)
  {
    return isset($this->processed[$file]);
  }

  protected function _locateFiles($name)
  {
    $locator = $this->toolkit->getFileLocator(lmb_env_get('LIMB_JS_INCLUDE_PATH'), 'js');
    return $locator->locateAll($name);
  }

  protected function _processInclude($filename)
  {
    try
    {
      $files = $this->_locateFiles(trim($filename, " \" '\r "));
    }
    catch(lmbException $e)
    {
      // temporarily stop and save exception to prevent preg_replace_callback warning
      if(!$this->error)
        $this->error = $e;

      return '';
    }

    return trim($this->processFiles($files));
  }
}


