<?php

include_once('limb/view/settings/macro.conf.php');

// Force every template to be recompiled on every request.
// Now recompiling is enabled only in debug mode.
$conf['forcecompile'] = lmbToolkit::instance()->isWebAppDebugEnabled();

