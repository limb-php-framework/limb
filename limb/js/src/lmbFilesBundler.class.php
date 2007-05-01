<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id$
 * @package    js
 */
lmb_require('limb/fs/src/lmbFs.class.php');

class lmbFilesBundler
{
  var $extractor;
  var $regex_match;
  var $root_files = array();
  var $bundled = array();
  var $file_stack = array();
  var $filters = array();

  function __construct($extractor = null)
  {
    $this->setDependecyExtractor($extractor);
  }

  function addRootFile($file)
  {
    $this->root_files[] = $file;
  }

  function setDependecyExtractor($extractor)
  {
    $this->extractor = $extractor;
  }

  function addFilter($filter)
  {
    $this->filters[] = $filter;
  }

  function createBundle()
  {
    $contents = '';
    $this->bundled = array();

    foreach($this->root_files as $root_file)
      $contents .= $this->_collectDependencies($root_file) . "\n";

    $this->_applyFilters($contents);
    return $contents;
  }

  function createBundleFile($bundle_file)
  {
    lmbFs :: mkdir(dirname($bundle_file));
    lmbFs :: safeWrite($bundle_file, $this->createBundle());
  }

  protected function _applyFilters(&$contents)
  {
    foreach(array_keys($this->filters) as $key)
      $this->filters[$key]->apply($contents);
  }

  protected function _collectDependencies($file)
  {
    $file = lmbFs :: normalizePath($file);

    if($this->_isBundled($file))
      return '';

    $contents = file_get_contents($file);
    $this->_markAsBundled($file);
    $this->_pushCurrentFile($file);

    $result = $contents;
    if($this->extractor)
    {
      $result = preg_replace_callback($this->extractor->getRegex() . 'm',
                                 array(&$this, '_replaceBundleEntry'),
                                 $contents);
    }

    $this->_popCurrentFile();
    return $result;
  }

  protected function _replaceBundleEntry($matches)
  {
    $dependency = $this->extractor->extractDependency($matches);
    return trim($this->_collectDependencies($dependency));
  }

  protected function _markAsBundled($file)
  {
    $this->bundled[$file] = 1;
  }

  protected function _isBundled($file)
  {
    return isset($this->bundled[$file]);
  }

  protected function _pushCurrentFile($file)
  {
    $this->file_stack[] =  $file;
  }

  protected function _popCurrentFile()
  {
    array_pop($this->file_stack);
  }

  protected function _getCurrentFile()
  {
    return end($this->file_stack);
  }
}

?>
