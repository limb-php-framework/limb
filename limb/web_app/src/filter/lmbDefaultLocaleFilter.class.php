<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

lmb_require('limb/filter_chain/src/lmbInterceptingFilter.interface.php');
lmb_require('limb/i18n/common.inc.php');

/**
 * class lmbDefaultLocaleFilter.
 *
 * @package web_app
 * @version $Id: lmbDefaultLocaleFilter.class.php 6352 2007-10-01 18:03:00Z pachanga $
 */
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
    $this->toolkit->setLocale($this->default_locale);
    $filter_chain->next();
  }
}


