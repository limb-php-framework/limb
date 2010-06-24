<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * class lmbLocaleSpec.
 *
 * @package i18n
 * @version $Id: lmbLocaleSpec.class.php 7486 2009-01-26 19:13:20Z pachanga $
 */
class lmbLocaleSpec
{
  const REGEX = "~^([a-zA-Z]+)           #language identifier
                  ([_]([a-zA-Z]+))?     #separator and the country
                  (\.([-a-zA-Z0-9]+))?   #separator and the charset
                  (@([a-zA-Z0-9]+))?     #separator and the variation
                  \$~x";

  protected $language = '';
  protected $country = '';
  protected $country_variation = '';
  protected $charset = '';
  protected $locale = '';
  protected $locale_string = '';

  function __construct($locale_string)
  {
    $this->locale_string = $locale_string;
    $this->_parse($locale_string);
  }

  function getLocaleString()
  {
    return $this->locale_string;
  }

  function getLanguage()
  {
    return $this->language;
  }

  function getCountry()
  {
    return $this->country;
  }

  function getCountryVariation()
  {
    return $this->country_variation;
  }

  function getCharset()
  {
    return $this->charset;
  }

  function getLocale()
  {
    return $this->locale;
  }

  protected function _parse($locale_string)
  {
    if(preg_match(self :: REGEX, $locale_string, $regs))
    {
      $this->language = strtolower($regs[1]);

      if(isset($regs[3]))
        $this->country = strtoupper($regs[3]);

      if(isset($regs[5]))
        $this->charset = strtolower($regs[5]);

      if(isset($regs[7]))
        $this->country_variation = strtolower($regs[7]);

      $this->locale = $this->language;
      if($this->country !== '')
        $this->locale .= '_' . $this->country;
    }
    else
    {
      $this->locale = strtolower($locale_string);
      $this->language = $this->locale;
    }
  }
}


