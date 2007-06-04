<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbDefaultLocaleFilter.class.php 5933 2007-06-04 13:06:23Z pachanga $
 * @package    $package$
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
    $this->toolkit->setLocale($this->default_locale);
    $filter_chain->next();
  }
}

?>