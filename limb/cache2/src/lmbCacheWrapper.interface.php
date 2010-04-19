<?php

lmb_require('limb/cache2/src/drivers/lmbCacheConnection.interface.php');

interface lmbCacheWrapper extends lmbCacheConnection
{
  function getWrappedConnection();
}