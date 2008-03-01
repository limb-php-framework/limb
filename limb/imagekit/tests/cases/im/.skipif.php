<?php
if(!extension_loaded('imagick'))
{
  echo "Imagick library tests are skipped since Imagick extension is disabled.\n";
  return true;
}
return false;

