<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbTestTreeDirNode.class.php 5417 2007-03-29 11:39:33Z pachanga $
 * @package    tests_runner
 */
require_once(dirname(__FILE__) . '/lmbTestGroup.class.php');
require_once(dirname(__FILE__) . '/lmbTestTreeNode.class.php');
require_once(dirname(__FILE__) . '/lmbTestTreeFileNode.class.php');
require_once(dirname(__FILE__) . '/lmbDetachedFixture.class.php');
require_once(dirname(__FILE__) . '/lmbTestFileFilter.class.php');
require_once(dirname(__FILE__) . '/lmbTestTreePath.class.php');

@define('LIMB_TEST_RUNNER_FILE_FILTER', '*Test.class.php;*.test.php;*_test.php');
@define('LIMB_TEST_RUNNER_CLASS_FORMAT', '%s.class.php');

class lmbTestTreeDirNode extends lmbTestTreeNode
{
  protected $dir;
  protected $file_filter;
  protected $class_format;
  protected $test_group;
  protected $loaded;
  protected $ignored;

  function __construct($dir, $file_filter = LIMB_TEST_RUNNER_FILE_FILTER, $class_format = LIMB_TEST_RUNNER_CLASS_FORMAT)
  {
    if(!is_dir($dir))
      throw new Exception("'$dir' is not a directory!");

    $this->dir = $dir;

    $this->file_filter = $this->_createFileFilter($file_filter);

    $this->class_format = $class_format;
  }

  function getDir()
  {
    return $this->dir;
  }

  function getChildren()
  {
    $this->_loadLazyChildren();
    return $this->children;
  }

  function getTestLabel()
  {
    $group = $this->createTestGroupWithoutChildren();
    return $group->getLabel();
  }

  function createTestGroup()
  {
    if(is_object($this->test_group))
      return $this->test_group;

    $this->test_group = $this->createTestGroupWithoutChildren();

    //actually a quick fix for a much deeper problem, right?
    if(!$this->_shouldIgnoreDir())
      $this->_addChildrenTestCases($this->test_group);

    return $this->test_group;
  }

  function bootstrap()
  {
    if(file_exists($this->dir . '/.init.php'))
      require_once($this->dir . '/.init.php');
  }

  function createTestGroupWithoutChildren()
  {
    //...the same story
    if($this->_shouldIgnoreDir())
      return new lmbTestGroup();

    $label = $this->_getDirectoryLabel();

    $group = new lmbTestGroup($label);

    $fixture = new lmbDetachedFixture($this->dir . '/.setup.php',
                                      $this->dir . '/.teardown.php');
    $group->useFixture($fixture);

    return $group;
  }

  protected function _getDirectoryLabel()
  {
    if(file_exists($this->dir . '/.description'))
      return file_get_contents($this->dir . '/.description');
    else
      return 'Group test in "' . $this->dir . '"';
  }

  protected function _createFileFilter($file_filter)
  {
    if(is_object($file_filter))
      return $file_filter;
    elseif(is_array($file_filter))
      return new lmbTestFileFilter($file_filter);
    else
      return new lmbTestFileFilter(explode(';', $file_filter));
  }

  protected function _addChildrenTestCases($group)
  {
    foreach($this->getChildren() as $child)
      $group->addTestCase($child->createTestGroup());
  }

  function _loadLazyChildren()
  {
    if(!is_null($this->loaded) && $this->loaded)
      return;

    if(!$this->_shouldIgnoreDir())
    {
      $dir_items = $this->getDirItems();

      foreach($dir_items as $item)
      {
        if(is_dir($item))
          $this->addChild(new lmbTestTreeDirNode($item, $this->file_filter, $this->class_format));
        else
          $this->addChild(new lmbTestTreeFileNode($item, $this->_extractClassName($item)));
      }
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

  protected function _shouldIgnoreDir()
  {
    if(!is_null($this->ignored))
      return $this->ignored;

    $this->bootstrap();

    $dir_items = scandir($this->dir);

    if(in_array('.ignore', $dir_items))
      $this->ignored = true;
    elseif(in_array('.ignore.php', $dir_items))
      $this->ignored = include($this->dir . "/.ignore.php");
    else
      $this->ignored = false;

    return $this->ignored;
  }

  protected function _isFileAllowed($file)
  {
    if($this->file_filter && !$this->file_filter->match($file))
        return false;

    return true;
  }

  protected function _extractClassName($file)
  {
    $regex = preg_quote($this->class_format);
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
