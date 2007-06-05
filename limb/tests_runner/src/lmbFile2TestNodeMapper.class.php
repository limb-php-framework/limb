<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
require_once(dirname(__FILE__) . '/lmbTestTreeDirNode.class.php');

class lmbFile2TestNodeMapper
{
  protected $file_filter;

  function map($start_dir, $file)
  {
    $start_dir = realpath($start_dir);
    $file = realpath($file);
    $file = preg_replace('~^' . preg_quote($start_dir) . '~', '', $file);

    $path_items = explode(DIRECTORY_SEPARATOR, $file);

    if(empty($path_items[0]))
      array_shift($path_items);

    $path = '/' . $this->_doMap($start_dir, $path_items, $found);
    if(!$found)
      return false;

    return $path;
  }

  function _doMap($dir, $path_items, &$mapped = false)
  {
    $counter = 0;
    $current_item = reset($path_items);

    $node = new lmbTestTreeDirNode($dir);

    foreach($node->getDirItems() as $item => $full_path)
    {
      if($item == $current_item)
      {
        if(sizeof($path_items) > 1 && is_dir($full_path))
        {
          array_shift($path_items);
          return $counter . '/' . $this->_doMap($full_path, $path_items, $mapped);
        }
        elseif(sizeof($path_items) == 1)
        {
          $mapped = true;
          return $counter;
        }
      }
      $counter++;
    }
  }
}

?>
