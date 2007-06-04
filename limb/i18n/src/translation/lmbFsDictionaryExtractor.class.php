<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbFsDictionaryExtractor.class.php 5933 2007-06-04 13:06:23Z pachanga $
 * @package    $package$
 */

class lmbFsDictionaryExtractor
{
  protected $parsers = array();

  function registerFileParser($ext, $parser)
  {
    $this->parsers[$ext] = $parser;
  }

  function traverse($iterator, &$dictionaries = array(), $response = null)
  {
    for($iterator->rewind(); $iterator->valid(); $iterator->next())
    {
      $item = $iterator->current();
      if($item->isFile())
      {
        $file = $item->getPathName();
        $ext = strrchr(basename($file), '.');
        if(isset($this->parsers[$ext]))
          $this->parsers[$ext]->extractFromFile($file, $dictionaries, $response);
      }
    }
  }
}

?>
