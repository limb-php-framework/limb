<?php
set_time_limit(0);

if($argc < 2)
{
  echo "Usage: show_new_packages <dir>";
  exit(1);
}

$PEAR = "http://pear.limb-project.com/index.php?rss&package=";

$root = $argv[1];
$dirs = explode("\n", trim(`find $root -maxdepth 1 -mindepth 1 -type d`));

foreach($dirs as $dir)
{
  $pkg = basename($dir);

  if(is_file("$dir/VERSION"))
  {
    list($name, $version, $status) = explode('-', trim(file_get_contents("$dir/VERSION")));

    if($status)
      $current = "$name $version ($status)";
    else
      $current = "$name $version";

    if(!$xml = get_url_contents($PEAR . $pkg))
    {
      echo "$pkg : Couldn't fetch latest version of '$pkg' from PEAR channel(new release '$current' ?)\n";
      continue;
    }
    if(!$rss = @simplexml_load_string($xml))
    {
      echo "$pkg : Couldn't parse packages XML of '$pkg' from PEAR channel(new release '$current' ?)\n";
      continue;
    }

    if(!isset($rss->item[0]))
    {
      echo "$pkg : Package '$pkg' had no releases\n";
      continue;
    }

    $latest = (string)$rss->item[0]->title;

    if($latest != $current)
      echo "$pkg : New release '$current' found, latest is '$latest'\n";
  }
}

function get_url_contents($url)
{
  if(!$proxy = getenv('http_proxy'))
    return file_get_contents($url);

  $curl = curl_init();
  curl_setopt($curl, CURLOPT_URL, $url);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($curl, CURLOPT_TIMEOUT, 2);
  curl_setopt($curl, CURLOPT_PROXY, $proxy);
  $page = trim(curl_exec($curl));
  curl_close($curl);
  return $page;
}

