<?php
set_time_limit(0);

if($argc < 2)
{
  echo "Usage: show_package_changelog <dir>";
  exit(1);
}

$dir = $argv[1];
`svn up $dir`;

$uri = get_repos_uri($dir);
$line = reset(explode("\n", `svn cat $uri/CHANGELOG`));

if(!preg_match('~\(r(\d+)\)~', $line, $m))
{
  echo "$last revision not found in CHANGELOG!\n";
  continue;
}
$last_changelog_rev = $m[1];

if($logs = get_svn_logs($dir, 'HEAD', $last_changelog_rev + 1))
{
  $date = date("j F Y");
  $next_rev = get_svn_revision($dir);

  $header = <<<EOD
x.x.x-? - $date (r$next_rev)
==================================================

EOD;
  echo $header;
}

foreach($logs as $item)
{
  echo $item[1] . "\n";
}

function get_svn_logs($path, $from, $to='HEAD')
{
  $xml = simplexml_load_string(`svn log --xml -r$from:$to $path`);
  $logs = array();
  foreach($xml->logentry as $entry)
  {
    $rev = (string)$entry['revision'];
    $msg = trim((string)$entry->msg);
    $logs[] = array($rev, $msg);
  }
  return $logs;
}

function get_repos_uri($svn_path)
{
  exec("svn info $svn_path", $out);

  foreach($out as $line)
  {
    if(preg_match('~URL:(.*)$~', $line, $m))
      return trim($m[1]);
  }
  return -1;
}

function get_svn_revision($svn_path)
{
  exec("svn info $svn_path", $out);

  foreach($out as $line)
  {
    if(preg_match('~Revision:(.*)$~', $line, $m))
      return trim($m[1]);
  }
  return -1;
}



