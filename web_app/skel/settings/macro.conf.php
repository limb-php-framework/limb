<?php

include_once('limb/view/settings/macro.conf.php');

// Recompiling templates is enabled only in debug mode.
$conf['forcecompile'] = lmbToolkit::instance()->isWebAppDebugEnabled();
