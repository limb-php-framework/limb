<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbFullPageCacheFilter.class.php 5555 2007-04-06 10:34:40Z pachanga $
 * @package    web_cache
 */
lmb_require('limb/filter_chain/src/lmbInterceptingFilter.interface.php');
lmb_require('limb/web_cache/src/lmbFullPageCacheRuleset.class.php');
lmb_require('limb/web_cache/src/lmbFullPageCache.class.php');
lmb_require('limb/web_cache/src/lmbFullPageCacheRequest.class.php');
lmb_require('limb/web_cache/src/lmbFullPageCacheUser.class.php');
lmb_require('limb/web_cache/src/lmbFullPageCacheWriter.class.php');
lmb_require('limb/web_cache/src/lmbFullPageCacheIniPolicyLoader.class.php');

class lmbFullPageCacheFilter implements lmbInterceptingFilter
{
  protected $user;
  protected $cache;
  protected $cache_dir;
  protected $rules_ini;

  function __construct($rules_ini = 'full_page_cache.ini', $cache_dir = null, $user = null)
  {
    $this->rules_ini = $rules_ini;

    if(!$cache_dir)
      $this->cache_dir = LIMB_VAR_DIR . '/fpcache/';
    else
      $this->cache_dir = $cache_dir;

    if(!is_object($user))
      $this->user = new lmbFullPageCacheUser();
    else
      $this->user = $user;
  }

  function run($filter_chain)
  {
    $toolkit = lmbToolkit :: instance();
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

?>