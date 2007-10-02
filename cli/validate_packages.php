<?php
set_time_limit(0);

if($argc < 2)
{
  echo "Usage: validate_packages <dir>";
  exit(1);
}

$root = $argv[1];
$dirs = explode("\n", trim(`find $root -maxdepth 1 -mindepth 1 -type d`));

foreach($dirs as $dir)
{
  $pkg = basename($dir);
  $ok = true;

  echo "Validating '$pkg': ";

  if(!is_file("$dir/CHANGELOG"))
    err("CHANGELOG missing, ", $ok);

  if(!is_file("$dir/VERSION"))
    err("VERSION missing, ", $ok);

  if(!is_file("$dir/DESCRIPTION"))
    err("DESCRIPTION missing, ", $ok);

  if(!is_file("$dir/SUMMARY"))
    err("SUMMARY missing, ", $ok);

  if(!is_file("$dir/MAINTAINERS"))
    err("MAINTAINERS missing, ", $ok);

  if(!is_file("$dir/package.php"))
    err("package.php missing, ", $ok);

  if(is_file("$dir/VERSION"))
  {
    list($name, $version) = explode('-', trim(file_get_contents("$dir/VERSION")));
    if($name != $pkg)
      err("VERSION file package name mismatch '$name', ", $ok);
  }

  if(is_file("$dir/CHANGELOG"))
  {
    $line = rtrim(reset(file("$dir/CHANGELOG")));
    if(preg_match('~^(\d+\.\d+\.\d+([-a-z]+)?)\s+-\s+\d+\s+[A-Za-z]+\s+\d\d\d\d\s+\(r\d+\)~', $line, $m))
    {
      if(is_file("$dir/VERSION"))
      {
        list($name, $version, $status) = explode('-', trim(file_get_contents("$dir/VERSION")));
        if($status)
          $version = "$version-$status";
        if($version != $m[1])
          err("CHANGELOG version '{$m[1]}' doesn't match VERSION '{$version}', ", $ok);
      }
    }
    else
      err("CHANGELOG first line is not well formed '$line', ", $ok);
  }

  if($ok)
    echo " OK ";
  else
    echo " ERROR ";

  echo " done\n";
}

function err($msg, &$ok)
{
  echo $msg;
  $ok = false;
}

