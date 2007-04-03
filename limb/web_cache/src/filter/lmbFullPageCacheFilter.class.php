<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbFullPageCacheFilter.class.php 5013 2007-02-08 15:38:13Z pachanga $
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

  function __construct($user = null)
  {
    if(is_null($user))
      $this->user = new lmbFullPageCacheUser();
    else
      $this->user = $user;
  }

  static function getCacheDir()
  {
    return LIMB_VAR_DIR . '/fpcache/';
  }

  static function getRulesIni()
  {
    return 'full_page_cache.ini';
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
    $loader = new lmbFullPageCacheIniPolicyLoader(self :: getRulesIni());
    return $loader->load();
  }

  protected function _createCacheWriter()
  {
    $writer = new lmbFullPageCacheWriter();
    $writer->setStorageDirectory(self :: getCacheDir());
    return $writer;
  }
}

?>