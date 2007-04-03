<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbSearchIndexingObserver.class.php 5014 2007-02-08 15:38:18Z pachanga $
 * @package    web_spider
 */

class lmbSearchIndexingObserver
{
  protected $counter = 0;
  protected $indexer;

  function __construct($indexer)
  {
    $this->indexer = $indexer;
  }

  function notify($reader)
  {
    $uri = $reader->getUri();

    $this->counter++;

    echo "\n{$this->counter})started indexing " . $uri->toString() . "...";

    $this->indexer->index($uri, $reader->getContent());

    echo "done";
  }
}

?>
