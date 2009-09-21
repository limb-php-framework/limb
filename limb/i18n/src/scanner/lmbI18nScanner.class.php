<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * class lmbI18nScanner.
 *
 * Scan i18n macro tags and lmb_i18n functions call in specified directory
 *
 * @package i18n
 * @version $Id: lmbI18nScanner.class.php 7994 2009-09-21 13:01:14Z idler $
 */
class lmbI18nScanner {

  protected $_dirs = array();

  protected $_found_files = array();

  protected $_messages = array();

  protected $_patterns = array(
    '#\{\{(__|i18n)[^}]+text=[\']([^\']+)[\']#is',
    '#\{\{(__|i18n)[^}]+text=["]([^"]+)["]#is',
    '#(lmb_i18n)\s*\(\s*[\']([^\']+)[\']#is',
    '#(lmb_i18n)\s*\(\s*["]([^"]+)["]#is'
  );
  
  function __construct($dirs)
  {
    $this->dirs = $dirs;
  }

  function scan()
  {
    $this->_found_files = array();
    foreach($this->dirs as $dir)
    {
      $this->scanForFiles($dir);
    }
  }

  function getFoundFiles()
  {
    return $this->_found_files;
  }

  protected function scanForFiles($dir)
  {
     $result = lmbFs :: findRecursive($dir, $types = 'f', $include_regex = '#.ph(tml|p)$#is');
     foreach($result as $name)
     {
       $this->_found_files[] = $name;
     }
  }


  function searchMessages()
  {
    foreach($this->_found_files as $file)
    {
      $content = file_get_contents($file);
      foreach($this->_patterns as $pattern)
      {
        preg_match_all($pattern, $content, $matches);
        
        foreach($matches[2] as $m)
        {
          if(empty($m)) continue;
          $this->addMessage($m);
        }
      }
    }
    
  }
  function getMessages()
  {
    return array_keys($this->_messages);
  }

  function addMessage($text)
  {
    $this->_messages[$text]=1;
  }

  function deleteMessage($text)
  {
    unset($this->_messages[$text]);
  }
}