<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/filter_chain/src/lmbFilterChain.class.php');
lmb_require('limb/net/src/lmbFakeHttpResponse.class.php');
lmb_require('limb/session/src/lmbFakeSession.class.php');
lmb_require('limb/web_app/src/lmbWebApplication.class.php');

class lmbWebApplicationSandbox extends lmbFilterChain
{
  protected $app;

  function __construct($app = null)
  {
    if(!is_object($app))
      $app = new lmbWebApplication();

    $this->app = $app;
  }

  function imitate($request)
  {
    $toolkit = lmbToolkit :: instance();
    $toolkit->setRequest($request);
    $toolkit->setResponse(new lmbFakeHttpResponse());
    $toolkit->setSession(new lmbFakeSession());

    $this->app->process();

    return $toolkit->getResponse();
  }
}


