<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

require_once('limb/wact/src/annotation/WactClassAnnotationParser.class.php');

/**
 * abstract class WactCompilerArtifactDictionary.
 *
 * @package wact
 * @version $Id: WactCompilerArtifactDictionary.class.php 7686 2009-03-04 19:57:12Z korchasa $
 */
abstract class WactCompilerArtifactDictionary
{
  protected $search_paths = array();
  protected $config;

  function __construct($config = null) {
    $this->config = $config;
  }

  abstract function _createArtifactsExtractor($file);

  function setConfig($config)
  {
    $this->config = $config;
  }

  function _prepareSearchPaths()
  {
    $paths = array();

    $include_path_array = explode(PATH_SEPARATOR, get_include_path());
    $include_path_array[] = '';

    foreach($include_path_array as $include_path_dir)
    {
      if ($include_path_dir)
        $include_path_dir .= '/';

      foreach($this->config->getScanDirectories() as $dir)
      {
        foreach($this->_getThisAndImmediateDirectories($include_path_dir . $dir) as $item)
          $paths[] = $item;
      }
    }
    $this->search_paths = $paths;
  }

  function _getThisAndImmediateDirectories($dir)
  {
    $dirs = array();
    foreach(glob("$dir/*") as $item) {
      if($item{0} != '.' && is_dir($item))
        $dirs[] = $item;
    }

    $dirs[] = $dir;

    return $dirs;
  }

  function extractItems($scandir, $extension)
  {
    $parser = new WactClassAnnotationParser();

    foreach(glob("$scandir/*$extension") as $file)
    {
      $extractor = $this->_createArtifactsExtractor($file);
      $parser->process($extractor, file_get_contents($file));
    }
  }

  function buildDictionary($extension)
  {
    $this->_prepareSearchPaths();

    foreach ($this->search_paths as $path)
      $this->extractItems($path, $extension);
  }
}

