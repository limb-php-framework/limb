<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * @package search
 * @version $Id: indexer.php 7686 2009-03-04 19:57:12Z korchasa $
 */
if(!isset($argv[1]))
  die("index starting uri not specified!\n");

$path = $_SERVER['LIMB_PROJECT_DIR'];
require_once($path . '/setup.php');

lmb_require('limb/net/src/lmbUri.class.php');
lmb_require('limb/web_spider/src/lmbWebSpider.class.php');
lmb_require('limb/web_spider/src/lmbUriFilter.class.php');
lmb_require('limb/web_spider/src/lmbContentTypeFilter.class.php');
lmb_require('limb/web_spider/src/lmbSearchIndexingObserver.class.php');
lmb_require('limb/search/src/indexer/lmbFullTextSearchIndexer.class.php');
lmb_require('limb/search/src/indexer/lmbSearchTextNormalizer.class.php');
lmb_require('limb/web_spider/src/lmbUriNormalizer.class.php');

$uri = new lmbUri($argv[1]);

$indexer = new lmbFullTextSearchIndexer(new lmbSearchTextNormalizer());
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


