<?php
set_time_limit(0);
$dir = $argv[1];
system('find ' . $dir . ' -type f -name "*.php" | xargs -i svn propset "svn:keywords" "Id" \'{}\'');
