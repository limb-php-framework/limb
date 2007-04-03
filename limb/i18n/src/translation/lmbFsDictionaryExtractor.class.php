<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbFsDictionaryExtractor.class.php 5359 2007-03-27 16:50:03Z pachanga $
 * @package    i18n
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
