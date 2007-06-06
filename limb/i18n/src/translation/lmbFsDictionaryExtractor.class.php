<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * class lmbFsDictionaryExtractor.
 *
 * @package i18n
 * @version $Id: lmbFsDictionaryExtractor.class.php 5945 2007-06-06 08:31:43Z pachanga $
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
