<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * interface lmbSearchIndexer.
 *
 * @package search
 * @version $Id: lmbSearchIndexer.interface.php 7486 2009-01-26 19:13:20Z pachanga $
 */
interface lmbSearchIndexer
{
  function index($uri, $content);
}


