<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbSearchIndexer.interface.php 5003 2007-02-08 15:36:51Z pachanga $
 * @package    search
 */
interface lmbSearchIndexer
{
  function index($uri, $content);
}

?>
