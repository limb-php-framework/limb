<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

require_once('limb/wact/src/compiler/filter/WactFilterDictionary.class.php');
require_once 'limb/wact/src/compiler/property/WactPropertyDictionary.class.php';
require_once 'limb/wact/src/compiler/tag_node/WactTagDictionary.class.php';
require_once 'limb/wact/src/compiler/WactCompiler.class.php';

/**
 * class WactDictionaryHolder.
 *
 * @package wact
 * @version $Id: WactDictionaryHolder.class.php 7686 2009-03-04 19:57:12Z korchasa $
 */
class WactDictionaryHolder
{
  protected $dictionaries;
  protected $config;
  protected static $instance = null;

  function __construct($config)
  {
    $this->config = $config;
  }

  static function initialize($config)
  {
    if(isset(self :: $instance))
      return self :: $instance;

    self :: $instance = new WactDictionaryHolder($config);
    self :: $instance->initializeAllDictionaries();

    return self :: $instance;
  }

  // for testing purpose
  static function resetInstance()
  {
    self :: $instance = null;
  }

  static function instance()
  {
    if(!isset(self :: $instance))
      throw new WactException('WactDictionaryHolder not initialized yet!');

    return self :: $instance;
  }

  function initializeAllDictionaries()
  {
    $this->initializeWactFilterDictionary();
    $this->initializePropertyDictionary();
    $this->initializeTagDictionary();
  }

  function initializeWactFilterDictionary()
  {
    $this->_initializeDictionary('filter', 'WactFilterDictionary', 'filter');
  }

  function initializePropertyDictionary()
  {
    $this->_initializeDictionary('property', 'WactPropertyDictionary', 'prop');
  }

  function initializeTagDictionary()
  {
    $this->_initializeDictionary('tag', 'WactTagDictionary', 'tag');
  }

  function getDictionary($name)
  {
    if(isset($this->dictionaries[$name]))
       return $this->dictionaries[$name];

    throw new WactException('Dictionary "' . $name . '" is not initialized yet!');
  }

  function getFilterDictionary()
  {
    return $this->getDictionary('filter');
  }

  function getTagDictionary()
  {
    return $this->getDictionary('tag');
  }

  function getPropertyDictionary()
  {
    return $this->getDictionary('property');
  }

  protected function _initializeDictionary($name, $dictionary_class, $type)
  {
    $dictionary = null;
    $cache_file = $this->config->getCacheDir() . "/{$dictionary_class}.cache";

    if(!$this->config->isForceScan() && file_exists($cache_file))
    {
      $dictionary = unserialize(file_get_contents($cache_file));
      $this->dictionaries[$name] = $dictionary;
    }

    if(!is_object($dictionary))
    {
      // For testing purposes
      if(isset($GLOBALS[$name.'_wact_dictionary']))
      {
        $dictionary = $GLOBALS[$name.'_wact_dictionary'];
        $dictionary->setConfig($this->config);
      }
      else
        $dictionary = new $dictionary_class($this->config);

      $this->dictionaries[$name] = $dictionary;
      $dictionary->buildDictionary('.' . $type . '.php');

      WactCompiler :: writeFile($cache_file, serialize($dictionary));
    }

    // For testing purposes
    $GLOBALS[$name.'_wact_dictionary'] = $dictionary;

    return $dictionary;
  }
}

