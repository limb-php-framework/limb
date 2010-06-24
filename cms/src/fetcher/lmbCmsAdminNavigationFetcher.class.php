<?php
lmb_require('limb/core/src/lmbCollection.class.php');
lmb_require('limb/web_app/src/fetcher/lmbFetcher.class.php');
lmb_require('limb/cms/src/exception/lmbCmsException.class.php');

class lmbCmsAdminNavigationFetcher extends lmbFetcher
{
  function _createDataSet()
  {
    $toolkit = lmbToolkit :: instance();
    $conf = $toolkit->getConf('navigation');

    $role = $toolkit->getCmsUser()->getRoleType();
    lmb_assert_true($conf->has($role), "Navigation section for current user role not found");
    $data = $conf->get($role);

    if(is_array($data))
      return new lmbCollection($data);
    else
      return new lmbCollection();
  }
}


