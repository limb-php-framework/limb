<?php
set_time_limit(0);

$packages = array(
"active_record",
"cache",
"calendar",
"cli",
//"cms",
"config",
"core",
"datetime",
"dbal",
"filter_chain",
"fs",
"i18n",
"imagekit",
"js",
"log",
"mail",
"net",
//"search",
"session",
"tests_runner",
"toolkit",
"tree",
"validation",
"view",
"wact",
"web_app",
"web_cache",
"web_spider",
"wysiwyg",
);

if($argc < 2)
{
  echo "Usage: collect_bundle <bundle_name> <limb_dir>";
  exit(1);
}

$name = $argv[1];
$root = $argv[2];

mkdir($name);
mkdir($name . '/limb');
$changelog = fopen("$name/limb/CHANGELOG", 'a');

foreach($packages as $pkg)
{
  echo "Bundling '$pkg'...";

  $dir = $root . '/' . $pkg;
  $dst = $name . '/limb/' . $pkg;

  if(is_file("$dir/CHANGELOG"))
  {
    fwrite($changelog, "\n################### Package '$pkg' ###################\n\n");
    fwrite($changelog, trim(file_get_contents("$dir/CHANGELOG")) . "\n");
  }
  `svn export $dir $dst`;

  echo "done.\n";
}

fclose($changelog);

gzip($name, $name . '.tgz');

function zip($file, $archive)
{
  echo "zipping $file => $archive\n";
  if(is_dir($file))
  {
    $old = getcwd();
    $dir = dirname($file);
    $name = basename($file);
    chdir($dir);
    `zip -r -9 -q $archive $name`;
    chdir($old);
  }
  else
    `cat $file | zip -9 -q > $archive`;
}

function gzip($file, $archive)
{
  echo "gzipping $file => $archive\n";
  if(is_dir($file))
  {
    $old = getcwd();
    $dir = dirname($file);
    $name = basename($file);
    chdir($dir);
    `tar cf - $name | gzip -9 -c > $archive`;
    chdir($old);
  }
  else
    `cat $file | gzip -9 -c > $archive`;
}

?>