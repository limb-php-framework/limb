<?php
if(!extension_loaded('gd'))
{
  echo "GD library tests are skipped since gd extension is disabled.\n";
  return true;
}
return false;

