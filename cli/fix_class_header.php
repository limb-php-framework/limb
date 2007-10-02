<?php
set_time_limit(0);
$dir = $argv[1];
$pkg = isset($argv[2]) ? $argv[2] : 'foo';
$files = explode("\n", trim(`find $dir -type f -name "*.php" | grep -v /lib/ | grep -v /settings/ | grep -v /shared/ | grep -v /www/ | grep -v /tests/`));

$class_regex = '~((?:\n|\r\n|\r)\s*(?:(?:abstract|final)\s+)?(?:class|interface)\s+\w+)~';
$doc_regex = '~(\/\*\*.*?\*\/)~s';

foreach($files as $file)
{
  $src = file_get_contents($file);
  $processed = $src;

  $items = preg_split($class_regex, $src, -1, PREG_SPLIT_DELIM_CAPTURE);

  if(sizeof($items) > 1)
  {
    $processed = '';
    $c = 0;
    foreach($items as $item)
    {
      if($c % 2 == 0 && isset($items[$c+1])) //skipping last item
      {
        if(!preg_match_all($doc_regex, $item, $matches))
        {
          $class = trim($items[$c+1]);
          $processed .= $item . "\n\n" . class_header($class, $pkg) . "\n";
        }
        else//normalizing class doc block
        {
          $old_doc_block = end($matches[1]);
          $new_doc_block = $old_doc_block;

          if(strpos($new_doc_block, '@package') === false)
            $new_doc_block = str_replace('*/', "* @package $pkg\n*/", $new_doc_block);

          if(strpos($new_doc_block, '@version') === false)
            $new_doc_block = str_replace('*/', "* @version \$Id\$\n*/", $new_doc_block);

          $new_doc_block = preg_replace('~(?:\n|\r\n|\r)\*~', "\n *", $new_doc_block);

          $processed .= str_replace($old_doc_block, "$new_doc_block\n", $item);
        }
      }
      elseif(isset($items[$c+1]))
        $processed .= ltrim($item, "\n\r\t");
      else
        $processed .= $item;

      $c++;
    }
  }

  if($src != $processed)
  {
    echo "writing changes to $file...";
    file_put_contents($file, $processed);
    echo "done\n";
  }
}

function class_header($description, $package = 'foo')
{
  $description = rtrim($description, '.') . '.';

  $tpl = <<<EOD
/**
 * $description
 *
 * @package $package
 * @version \$Id\$
 */
EOD;
  return $tpl;
}


