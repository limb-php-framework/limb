<?php

if(count($argv) == 1)
  die('Usage: '.$argv[0].' <connection url> <iterations count>'.PHP_EOL);

//$iterations

define('ITERATIONS_COUNT', $argv[2]);

require('bench.inc.php');
require_once('limb/cache2/src/lmbCacheFactory.class.php');

$connection = lmbCacheFactory::createConnection($argv[1]);
echo '==='.$connection->getType().'==='.PHP_EOL;
draw_text_report(bench_cacher($connection));



