<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * class lmbFsDictionaryExtractor.
 *
 * @package i18n
 * @version $Id: lmbFsDictionaryExtractor.class.php 7486 2009-01-26 19:13:20Z pachanga $
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
    foreach($iterator as $file)
    {
      if($iterator->isFile())
      {
        $ext = strrchr(basename($file), '.');
        if(isset($this->parsers[$ext]))
          $this->parsers[$ext]->extractFromFile($file, $dictionaries, $response);
      }
    }
  }
}


