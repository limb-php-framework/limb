#!/usr/bin/env php
<?php

$project_dir = realpath(dirname(__FILE__) . '/../');
require_once($project_dir . '/setup.php');

require_once('limb/taskman/taskman.inc.php');

require_once('limb/net/src/lmbUri.class.php');
require_once('limb/web_spider/src/lmbWebSpider.class.php');
require_once('limb/web_spider/src/lmbUriFilter.class.php');
require_once('limb/web_spider/src/lmbContentTypeFilter.class.php');
require_once('limb/web_spider/src/lmbSearchIndexingObserver.class.php');
require_once('limb/web_spider/src/lmbUriNormalizer.class.php');
require_once('limb/zfsearch/src/indexer/lmbZendSearchIndexer.class.php');

taskman_propset('LIMB_VAR_DIR', $project_dir . '/var');
taskman_run();

function task_index($args)
{
  if(!isset($args[0]) || !$args[0])
  {
    taskman_sysmsg("Index starting uri not specified!\n");
    exit(1);
  }

  set_time_limit(0);
  ini_set('memory_limit', '512M');

  $uri = new lmbUri($args[0]);

  taskman_sysmsg("Indexing...\n");

  $indexer = new lmbZendSearchIndexer();
  $indexer->useNOINDEX();

  $observer = new lmbSearchIndexingObserver($indexer);

  $content_type_filter = new lmbContentTypeFilter();
  $content_type_filter->allowContentType('text/html');

  $uri_filter = new lmbUriFilter();
  $uri_filter->allowHost($uri->getHost());
  $uri_filter->allowProtocol('http');

  $uri_filter->allowPathRegex('~.*~');
  $uri_filter->disallowPathRegex('~/admin.*~');

  $normalizer = new lmbUriNormalizer();
  $normalizer->stripQueryItem('PHPSESSID');

  $spider = new lmbWebSpider();
  $spider->setContentTypeFilter($content_type_filter);
  $spider->setUriFilter($uri_filter);
  $spider->setUriNormalizer($normalizer);
  $spider->registerObserver($observer);

  $spider->crawl($uri);

  taskman_sysmsg("\nDone\n\n");
}

function task_usage()
{
  $msg =

  "Usage:\n" .
  "  $ php indexer.php index <site_url>\n\n" .
  "Creates search index from <site_url>\n" .
  "For example:\n" .
  "  $ php indexer.php index http://limb-project.com\n\n";

  taskman_msg($msg);
}