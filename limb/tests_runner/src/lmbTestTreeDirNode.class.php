<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
require_once(dirname(__FILE__) . '/lmbTestTreeShallowDirNode.class.php');
require_once(dirname(__FILE__) . '/lmbTestTreeFileNode.class.php');
require_once(dirname(__FILE__) . '/lmbDetachedFixture.class.php');
require_once(dirname(__FILE__) . '/lmbTestFileFilter.class.php');

/**
 * class lmbTestTreeDirNode.
 *
 * @package tests_runner
 * @version $Id: lmbTestTreeDirNode.class.php 6021 2007-06-28 13:18:44Z pachanga $
 */
class lmbTestTreeDirNode extends lmbTestTreeShallowDirNode
{
  protected static $file_filter = '*Test.class.php;*.test.php;*_test.php';
  protected static $class_format = '%s.class.php';
  protected $loaded;

  function createTestCase()
  {
    $this->_loadChildren();
    return parent :: createTestCase();
  }

  static function getFileFilter()
  {
    if(is_object(self :: $file_filter))
      return self :: $file_filter;
    elseif(is_array(self :: $file_filter))
      return new lmbTestFileFilter(self :: $file_filter);
    else
      return new lmbTestFileFilter(explode(';', self :: $file_filter));
  }

  static function setFileFilter($filter)
  {
    $prev = self :: getFileFilter();
    self :: $file_filter = $filter;
    return $prev;
  }

  static function getClassFormat()
  {
    return self :: $class_format;
  }

  static function setClassFormat($format)
  {
    $prev = self :: $class_format;
    self :: $class_format = $format;
    return $prev;
  }

  function _loadChildren()
  {
    if(!is_null($this->loaded) && $this->loaded)
      return;

    $dir_items = $this->getDirItems();

    foreach($dir_items as $item)
    {
      if(is_dir($item))
        $this->addChild(new lmbTestTreeDirNode($item));
      else
        $this->addChild(new lmbTestTreeFileNode($item, $this->_extractClassName($item)));
    }
    $this->loaded = true;
  }

  function getDirItems()
  {
    $clean_and_sorted = array();
    $dir_items = scandir($this->dir);

    foreach($dir_items as $item)
    {
      if($item{0} == '.' || (!is_dir($this->dir . '/' . $item) && !$this->_isFileAllowed($item)))
        continue;
      $clean_and_sorted[$item] = $this->dir . '/' . $item;
    }

    uasort($clean_and_sorted, array($this, '_dirSorter'));
    return $clean_and_sorted;
  }

  protected function _isFileAllowed($file)
  {
    $filter = self :: getFileFilter();

    if($filter && !$filter->match($file))
      return false;
    return true;
  }

  protected function _extractClassName($file)
  {
    $regex = preg_quote(self :: $class_format);
    $regex = '~^' . str_replace('%s', '(.*)', $regex) . '$~';

    if(preg_match($regex, basename($file), $m))
      return $m[1];
  }

  protected function _dirSorter($a, $b)
  {
    if(is_dir($a) && !is_dir($b))
      return -1;
    elseif(!is_dir($a) && is_dir($b))
      return 1;
    return strcmp($a, $b);
  }
}

?>
