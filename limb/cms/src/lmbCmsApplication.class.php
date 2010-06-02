<?php
lmb_require('limb/web_app/src/lmbWebApplication.class.php');

class lmbCmsApplication extends lmbWebApplication
{
	function __construct()
	{
		$this->addPreActionFilter(new lmbHandle('limb/cms/src/filter/lmbCmsAccessPolicyFilter'));
	}

  protected function _getRequestDispathingFilter()
  {
    return new lmbHandle('limb/cms/src/filter/lmbCmsRequestDispatchingFilter');
  }
}

