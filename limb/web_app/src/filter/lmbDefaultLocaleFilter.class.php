<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbDefaultLocaleFilter.class.php 5012 2007-02-08 15:38:06Z pachanga $
 * @package    web_app
 */

lmb_require('limb/filter_chain/src/lmbInterceptingFilter.interface.php');

class lmbDefaultLocaleFilter implements lmbInterceptingFilter
{
  protected $default_locale;
  protected $toolkit;

  function __construct($default_locale)
  {
    $this->default_locale = $default_locale;
    $this->toolkit = lmbToolkit :: instance();
  }

  function run($filter_chain)
  {
    $this->_setDefaultLocale();
    $filter_chain->next();
  }

  protected function _setDefaultLocale()
  {
    $this->_setLocaleByLocaleString($this->default_locale);
  }

  protected function _setLocaleByLocaleString($locale_string)
  {
    $locale = $this->toolkit->createLocale($locale_string);
    $this->toolkit->setLocale($locale);
  }
}

?>