<?php

if(count($argv) == 1)
  die('Usage: '.$argv[0].' <connection url> <iterations count>'.PHP_EOL);

//$iterations

define('ITERATIONS_COUNT', $argv[2]);
define("LIMB_DB_DSN", "mysqli://root:test@localhost/limb_tests");

$limb_dir = realpath(dirname(__FILE__).'/../../../../');
set_include_path($limb_dir . PATH_SEPARATOR . get_include_path());

require_once("limb/dbal/common.inc.php");
require_once("limb/web_app/common.inc.php");

require('bench.inc.php');
require_once('limb/cache2/src/lmbCacheFactory.class.php');

$connection = lmbCacheFactory::createConnection($argv[1]);

echo '==='.$connection->getType().'==='.PHP_EOL;

draw_text_report(bench_cacher($connection));
