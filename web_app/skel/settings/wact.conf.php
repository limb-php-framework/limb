<?php

include_once('limb/view/settings/wact.conf.php');

// Recompiling templates is enabled only in debug mode.
$conf['forcecompile'] = lmbToolkit::instance()->isWebAppDebugEnabled();
