<?php
set_time_limit(0);

if($argc < 2)
{
  echo "Usage: show_release_candidates <dir>";
  exit(1);
}

$root = $argv[1];
$exclude = isset($argv[2]) ? $argv[2] : '';
$dirs = explode("\n", trim(`find $root -maxdepth 1 -mindepth 1 -type d`));

foreach($dirs as $dir)
{
  $pkg = basename($dir);

  if(is_file("$dir/CHANGELOG"))
  {
    $line = reset(file("$dir/CHANGELOG"));
    if(!preg_match('~\(r(\d+)\)~', $line, $m))
    {
      echo "$last revision not found in CHANGELOG!\n";
      continue;
    }
    $last_changelog_rev = $m[1];

    //use repos uri or WC?
    //$uri = get_repos_uri($dir);
    //get_svn_logs($uri, $m[1], 'HEAD');

    $is_candidate = false;
    $logs = get_svn_logs($dir, 'HEAD', $last_changelog_rev + 1);
    $log_string = '';
    foreach($logs as $item)
    {
      list($rev, $msg) = $item;
      $lines = filter_msg_lines(explode("\n", $msg), $exclude);

      if($lines)
      {
        $log_string .= "r$rev\n" . implode("\n", $lines). "\n";
        $is_candidate = true;
        break;
      }
    }
    if($is_candidate)
    {
      echo "================================================\n";
      echo "$pkg : most probaly a candidate for release(since r$last_changelog_rev), see log:\n$log_string\n\n";
    }
  }
}

function filter_msg_lines($lines, $exclude = '')
{
  $filtered = array();

  foreach($lines as $line)
  {
    if($exclude && preg_match("~$exclude~", $line))
      continue;
    $filtered[] = $line;
  }
  return $filtered;
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


