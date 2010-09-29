<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/filter_chain/src/lmbInterceptingFilter.interface.php');
lmb_require('limb/web_cache/src/lmbFullPageCacheRuleset.class.php');
lmb_require('limb/web_cache/src/lmbFullPageCache.class.php');
lmb_require('limb/web_cache/src/lmbFullPageCacheRequest.class.php');
lmb_require('limb/web_cache/src/lmbFullPageCacheUser.class.php');
lmb_require('limb/web_cache/src/lmbFullPageCacheWriter.class.php');
lmb_require('limb/web_cache/src/lmbFullPageCacheIniPolicyLoader.class.php');

/**
 * class lmbFullPageCacheFilter.
 *
 * @package web_cache
 * @version $Id: lmbFullPageCacheFilter.class.php 7686 2009-03-04 19:57:12Z korchasa $
 */
class lmbFullPageCacheFilter implements lmbInterceptingFilter
{
  protected $user;
  protected $cache;
  protected $cache_dir;
  protected $rules_ini;

  function __construct($rules_ini = 'full_page_cache.ini', $cache_dir = null, $user = null)
  {
    $this->rules_ini = $rules_ini;
	$this->cache_dir = $cache_dir ? $cache_dir : lmbToolkit::instance()->getConf('web_cache')->get('cache_dir');

    if(!is_object($user))
      $this->user = new lmbFullPageCacheUser();
    else
      $this->user = $user;
  }

  function run($filter_chain)
  {
    $toolkit = lmbToolkit :: instance();
    if ($toolkit->isWebAppDebugEnabled())
	  return $filter_chain->next();
		
    $request = $toolkit->getRequest();

    $this->cache = new lmbFullPageCache($this->_createCacheWriter(),
                                        $this->_createCachePolicy());

    $cache_request = new lmbFullPageCacheRequest($request, $this->user);
    if(!$this->cache->openSession($cache_request))
    {
      $filter_chain->next();
      return;
    }

    $response = $toolkit->getResponse();
    if($content = $this->cache->get())
    {
      $response->write($content);
      $response->commit();
    }
    else
    {
      $filter_chain->next();
      $content = $response->getResponseString();
      $this->cache->save($content);
    }
  }

  protected function _createCachePolicy()
  {
    $loader = new lmbFullPageCacheIniPolicyLoader($this->rules_ini);
    return $loader->load();
  }

  protected function _createCacheWriter()
  {
    $writer = new lmbFullPageCacheWriter($this->cache_dir);
    return $writer;
  }
}


