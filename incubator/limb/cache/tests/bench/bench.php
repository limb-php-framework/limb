<?php

set_include_path(dirname(__FILE__) . '/../../../../');

require_once('limb/core/common.inc.php');
require_once('limb/cache/common.inc.php');
require_once('limb/cache/common.inc.php');
lmb_require('limb/cache/src/lmbCacheFileBackend.class.php');
lmb_require('limb/cache/src/lmbCacheFileWithMetaBackend.class.php');

$cache_my=new lmbCacheFileWithMetaBackend(dirname(__FILE__)."/cache1");
$cache=new lmbCacheFileBackend(dirname(__FILE__)."/cache2");

$cache->flush();
$cache_my->flush();

echo "All digits is seconds per reading\n";

echo "Reading Same Variable from small cache \n";

$var= array(rand(),rand(),rand(),rand(),rand(),rand(),rand(),rand());
$cache_my->set('foo',$var);
$cache->set('foo',$var);

$mark = microtime(true);
$n=5000; //number of readings
for($i=0;$i<$n;$i++)
{
  $d=$cache_my->get('foo');
}
$t1=(microtime(true) - $mark)/$n;
echo "lmbCacheFileWithMetaBackend: " . $t1 . "  ";

$mark = microtime(true);
for($i=0;$i<$n;$i++)
{
  $d=$cache->get('foo');
}
$t2=(microtime(true) - $mark)/$n;
echo "lmbCacheFileBackend: " . $t2 . "  second/first=".$t2/$t1."\n";

$cache->flush();
$cache_my->flush();

echo "Reading Same Variable from big cache \n";

$n=500;// number of riadings and files in cache
$m=10; // number different experiments


for($i=0;$i<$n;$i++)
{
  $var= array(rand(),rand(),rand(),rand(),rand(),rand(),rand(),rand(),rand(),rand(),rand(),rand(),rand());
  $cache_my->set($i,$var);
  $cache->set($i,$var);
}

$sredn1=0; $sredn2=0;
for ($j=0;$j<$m;$j++)
{
  $key=rand(0,$n);

  $mark = microtime(true);
  for($i=0;$i<$n;$i++)
  {
    $d1=$cache_my->get($key);
  }
  $t=microtime(true) - $mark; $sredn1+=$t;
  $mark = microtime(true);
  for($i=0;$i<$n;$i++)
  {
    $d2=$cache->get($key);
  }
  $t=microtime(true) - $mark; $sredn2+=$t;
}
echo "lmbCacheFileWithMetaBackend: " . $sredn1/$m/$n . "     ";
echo "lmbCacheFileBackend: " . $sredn2/$m/$n . "\n";
echo "second/first=".$sredn2/$sredn1. "\n";
$cache->flush();
$cache_my->flush();

echo "Reading Same Variable from big cache With ttl \n";
//создаем большой кеш

for($i=0;$i<$n;$i++)
{
  $ttl=rand(3600,80000);
  $var= array(rand(),rand(),rand(),rand(),rand(),rand(),rand(),rand(),rand(),rand(),rand(),rand(),rand());
  $cache_my->set($i,$var,array('ttl'=>$ttl));
  $cache->set($i,$var,array('ttl'=>$ttl));
}

$sredn1=0;
$sredn2=0;

for ($j=0;$j<$m;$j++)
{
  $key=rand(0,$n);

  $mark = microtime(true);
  for($i=0;$i<$n;$i++)
  {
    $d1=$cache_my->get($key);
  }
  $t=microtime(true) - $mark; $sredn1+=$t;

  $mark = microtime(true);
  for($i=0;$i<$n;$i++)
  {
    $d2=$cache->get($key);
  }
  $t=microtime(true) - $mark; $sredn2+=$t;
}
echo "lmbCacheFileWithMetaBackend: " . $sredn1/$m/$n . "     ";
echo "lmbCacheFileBackend: " . $sredn2/$m/$n . "\n";
echo "second/first=".$sredn2/$sredn1. "\n";

echo "Reading Same Variable from big cache. Variable not found. \n";

$sredn1=0;$sredn2=0;
for ($j=0;$j<$m;$j++)
{
  $mark = microtime(true);
  for($i=0;$i<$n;$i++)
  {
    $d1=$cache_my->get('foo');
  }
  $t=microtime(true) - $mark; $sredn1+=$t;

  $mark = microtime(true);
  for($i=0;$i<$n;$i++)
  {
    $d2=$cache->get('foo');
  }
  $t=microtime(true) - $mark; $sredn2+=$t;
}
echo "lmbCacheFileWithMetaBackend: " . $sredn1/$m/$n . "     ";
echo "lmbCacheFileBackend: " . $sredn2/$m/$n . "\n";
echo "second/first=".$sredn2/$sredn1. "\n";


$cache->flush();
$cache_my->flush();


