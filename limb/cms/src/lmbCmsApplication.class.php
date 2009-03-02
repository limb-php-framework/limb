<?php
lmb_require('limb/filter_chain/src/lmbFilterChain.class.php');
lmb_require('limb/core/src/lmbHandle.class.php');

class lmbCmsApplication extends lmbFilterChain
{
  function __construct()
  {
    $this->registerFilter(new lmbHandle('limb/web_app/src/filter/lmbErrorHandlingFilter'));
    $this->registerFilter(new lmbHandle('limb/dbal/src/filter/lmbAutoDbTransactionFilter'));
    $this->registerFilter(new lmbHandle('limb/web_app/src/filter/lmbSessionStartupFilter'));
    $this->registerFilter(new lmbHandle('limb/cms/src/filter/lmbCmsRequestDispatchingFilter'));
    $this->registerFilter(new lmbHandle('limb/web_app/src/filter/lmbResponseTransactionFilter'));
    $this->registerFilter(new lmbHandle('limb/cms/src/filter/lmbCmsAccessPolicyFilter'));
    $this->registerFilter(new lmbHandle('limb/web_app/src/filter/lmbActionPerformingFilter'));
    $this->registerFilter(new lmbHandle('limb/web_app/src/filter/lmbViewRenderingFilter'));
  }
}

