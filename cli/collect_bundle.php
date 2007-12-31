<?php
set_time_limit(0);

$packages = array(
"active_record",
"cache",
"calendar",
"cli",
"config",
"core",
"datetime",
"dbal",
"filter_chain",
"fs",
"i18n",
"js",
"log",
"macro",
"mail",
"net",
"session",
"tests_runner",
"toolkit",
"tree",
"validation",
"view",
"wact",
"web_app",
"web_cache",
"wysiwyg",
);

if($argc < 2)
{
  echo "Usage: collect_bundle <bundle_name> <limb_dir>";
  exit(1);
}

$name = $argv[1];
$root = $argv[2];

$build_dir = dirname(__FILE__) . '/build';
$release_dir = $build_dir . '/' . $name;
@mkdir($build_dir);
mkdir($release_dir);
mkdir($release_dir . '/limb');

foreach($packages as $pkg)
{
  echo "Bundling '$pkg'...";

  $dir = $root . '/' . $pkg;
  $dst = $release_dir . '/limb/' . $pkg;
  `svn export $dir $dst`;

  echo "done.\n";
}

`svn export $root/CHANGELOG $release_dir/CHANGELOG`;
`svn export $root/README $release_dir/README`;
`svn export $root/LICENSE $release_dir/LICENSE`;

gzip($release_dir, $build_dir . '/' . $name . '.tgz');

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

