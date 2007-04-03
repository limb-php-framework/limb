<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbTestTree.class.php 5006 2007-02-08 15:37:13Z pachanga $
 * @package    tests_runner
 */

class lmbTestTree
{
  protected $root_node;
  protected $start_time = 0;
  protected $stop_time = 0;

  function __construct($root_node)
  {
    $this->root_node = $root_node;
  }

  function getElapsedTime()
  {
    return number_format(((substr($this->stop_time, 0, 9)) +
        (substr($this->stop_time,-10)) -
        (substr($this->start_time, 0, 9)) - (substr($this->start_time, -10))), 4);
  }

  protected function _startTiming()
  {
    $this->start_time = microtime();
  }

  protected function _stopTiming()
  {
    $this->stop_time = microtime();
  }

  function find($path)
  {
    $node = $this->root_node->findChildByPath($path);
    $this->root_node->bootstrapPath($path);
    return $node;
  }

  function perform($path, $reporter)
  {
    try
    {
      return $this->_doPerform($path, $reporter);
    }
    catch(Exception $e)
    {
      $this->_showException($e);
      return false;
    }
  }

  protected function _doPerform($path, $reporter)
  {
    $this->_startTiming();

    if(!$node = $this->find($path))
      throw new Exception("Test node '$path' not found!");

    $test = $node->createTestGroupWithParents();
    $res = $test->run($reporter);

    $this->_stopTiming();

    return $res;
  }

  protected function _showException($e)
  {
    echo $e->__toString();
  }
}

?>