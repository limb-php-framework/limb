<?php
/*
  Parameters:
  -d - Enable debug mode. Don't make any record in log and show output.
  -v - Enabled start and end job logging
*/

error_reporting(E_ALL);
ini_set('display_errors', true);

if($argc < 2)
  die('Usage: '.$argv[0].' cron_job_name' . PHP_EOL);

lmb_require('cron/src/lmbCronJobRunner.class.php');
lmb_require('cron/src/lmbCronJobLogger.class.php');

$cron_job_file = $argv[1];

$logger = new lmbCronJobLogger($cron_job_file);
$runner = new lmbCronJobRunner($logger, $cron_job_file);
$runner->setDebugMode(in_array('-d', $argv));
$runner->fullLogMode(!in_array('-v', $argv));
$runner->run();
