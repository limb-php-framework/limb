<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
/**
 * @package web_app
 * @version $Id$
 */
lmb_require('limb/toolkit/src/lmbAbstractTools.class.php');

class lmbProfileTools extends lmbAbstractTools
{
  protected $profile_reporter;

  protected function _createProfileReporterFromConfig()
  {
  	$toolkit = lmbToolkit::instance();

  	$profiler_class = 'lmbProfilePanelReporter';
  	$options = array();
  	if($toolkit->hasConf('profile'))
  	{
      $conf = $toolkit->getConf('profile');
      $options = (isset($conf['options'])) ? $conf['options'] : array();
      if(isset($conf['profile_reporter']))
        $profiler_class = $conf['profile_reporter'];
  	}

  	lmb_require('limb/profile/src/'.$profiler_class.'.class.php');

    return new $profiler_class($options);
  }

  /**
   * @return lmbProfileReporterInterface
   */
  function getProfileReporter()
  {
  	if(is_null($this->profile_reporter))
  		$this->profile_reporter = $this->_createProfileReporterFromConfig();

  	return $this->profile_reporter;
  }

  /**
   * @return lmbProfileReporterInterface
   */
  function isProfilingEnabled()
  {
    $toolkit = lmbToolkit::instance();
    if(!$toolkit->hasConf('profile'))
      return false;
    return $toolkit->getConf('profile')->get('enabled', false);
  }
}
