<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
 /**
 * class ZendSearchCliCmd
 *
 * @package zfsearch
 * @version $Id$
 */
class ZendSearchCliCmd extends lmbCliBaseCmd
{
  function execute($argv)
  {
    $this->help($argv);
  }

  function index($argv)
  {
    $input = new lmbCliInput();
    $input->setMinimumArguments(1);
    if(!$input->read($argv, false))
    {
      echo "Index starting uri not specified!\n";
      return 1;
    }
    
    set_time_limit(0);
    ini_set('memory_limit', '512M');

    require_once('limb/net/src/lmbUri.class.php');
    require_once('limb/web_spider/src/lmbWebSpider.class.php');
    require_once('limb/web_spider/src/lmbUriFilter.class.php');
    require_once('limb/web_spider/src/lmbContentTypeFilter.class.php');
    require_once('limb/web_spider/src/lmbSearchIndexingObserver.class.php');
    require_once('limb/web_spider/src/lmbUriNormalizer.class.php');
    require_once('limb/zfsearch/src/indexer/lmbZendSearchIndexer.class.php');
    
    $uri = new lmbUri($input->getArgument(0));
    
    echo "Indexing...\n";
    
    $indexer = new lmbZendSearchIndexer();
    $indexer->useNOINDEX();
     
    $observer = new lmbSearchIndexingObserver($indexer);
     
    $content_type_filter = new lmbContentTypeFilter();
    $content_type_filter->allowContentType('text/html');
     
    $uri_filter = new lmbUriFilter();
    $uri_filter->allowHost($uri->getHost());
    $uri_filter->allowProtocol('http');
    $uri_filter->allowPathRegex('~.*~');
     
    $normalizer = new lmbUriNormalizer();
    $normalizer->stripQueryItem('PHPSESSID');
     
    $spider = new lmbWebSpider();
    $spider->setContentTypeFilter($content_type_filter);
    $spider->setUriFilter($uri_filter);
    $spider->setUriNormalizer($normalizer);
    $spider->registerObserver($observer);
 
    $spider->crawl($uri);

    echo "\ndone!";
  }
  
  function help($argv)
  {
    $txt = <<<EOD
Usage:
  zend_search index <site_url>

  Creates search index from <site_url>. 
  For example: zend_search index http://mysite.com

EOD;
    echo $txt;
  }
}


