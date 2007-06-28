<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
require_once(dirname(__FILE__). '/lmbTestTreeNode.class.php');
require_once(dirname(__FILE__). '/lmbTestTreeFilePathNode.class.php');

/**
 * class lmbTestTreeGlobNode.
 *
 * @package tests_runner
 * @version $Id: lmbTestTreeGlobNode.class.php 6021 2007-06-28 13:18:44Z pachanga $
 */
class lmbTestTreeGlobNode extends lmbTestTreeNode
{
  protected $paths;

  function __construct($paths)
  {
    if(!is_array($paths))
      $paths = array($paths);
    $this->paths = $paths;
  }

  protected function _loadChildren()
  {
    foreach($this->paths as $path)
    {
      foreach(glob($path) as $item)
        $this->addChild(new lmbTestTreeFilePathNode($item));
    }
  }

  function getTestLabel()
  {
    return 'All ' . implode(';', $this->paths) . ' tests ';
  }
}

?>
