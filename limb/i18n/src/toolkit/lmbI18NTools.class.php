<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbI18NTools.class.php 5415 2007-03-29 10:14:35Z pachanga $
 * @package    i18n
 */
lmb_require('limb/toolkit/src/lmbAbstractTools.class.php');
lmb_require('limb/i18n/src/locale/lmbLocale.class.php');
lmb_require('limb/i18n/src/translation/lmbQtDictionaryBackend.class.php');

class lmbI18NTools extends lmbAbstractTools
{
  protected $current_locale;
  protected $locale_objects = array();
  protected $dictionaries = array();
  protected $dict_backend;

  function getDictionaryBackend()
  {
    if(!is_object($this->dict_backend))
    {
      $this->dict_backend = new lmbQtDictionaryBackend();
    }

    return $this->dict_backend;
  }

  function setDictionaryBackend($backend)
  {
    $this->dict_backend = $backend;
  }

  function getLocale()
  {
    if(!$this->current_locale)
      $this->current_locale = 'en_US';

    return $this->current_locale;
  }

  function setLocale($locale)
  {
    $this->current_locale = $locale;
  }

  function getLocaleObject($locale = null)
  {
    if(!$locale)
      $locale = $this->getLocale();

    if(!isset($this->locale_objects[$locale]))
      $this->locale_objects[$locale] = lmbLocale :: create($locale);

    return $this->locale_objects[$locale];
  }

  function addLocaleObject($obj, $locale = null)
  {
    if(!$locale)
      $locale = $obj->getLocaleString();

    $this->locale_objects[$locale] = $obj;
  }

  function createLocaleObject($locale)
  {
    return lmbLocale :: create($locale);
  }

  function getDictionary($locale, $domain)
  {
    if(!isset($this->dictionaries[$locale . '@' . $domain]))
    {
      $backend = $this->getDictionaryBackend();
      $this->dictionaries[$locale . '@' . $domain] = $backend->load($locale, $domain);
    }

    return $this->dictionaries[$locale . '@' . $domain];
  }

  function setDictionary($locale, $domain, $dict)
  {
    $this->dictionaries[$locale . '@' . $domain] = $dict;
  }

  function translate($text, $arg1 = null, $arg2 = null)
  {
    $locale = $this->getLocale();

    $domain = 'default';
    $attributes = null;

    if(is_array($arg1))
    {
      $attributes = $arg1;
      if(is_string($arg2))
        $domain = $arg2;
    }
    elseif(is_string($arg1))
      $domain = $arg1;

    if($dict = $this->getDictionary($locale, $domain))
      return $dict->translate($text, $attributes);
    else
      return $text;
  }
}
?>
