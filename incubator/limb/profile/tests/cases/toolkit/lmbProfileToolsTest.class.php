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
lmb_require('limb/toolkit/src/lmbMockToolsWrapper.class.php');
lmb_require('limb/profile/src/toolkit/lmbProfileTools.class.php');

class lmbProfileToolsTest extends UnitTestCase
{
	/**
	 * @var lmbProfileTools
	 */
	protected $toolkit;

  function setUp()
  {
    lmbToolkit :: save();
    lmbToolkit :: merge(new lmbProfileTools());
    $this->toolkit = lmbToolkit :: instance();
    $this->toolkit->setConfIncludePath('');
  }

  function tearDown()
  {
    lmbToolkit :: restore();
  }

  function testGetProfileReporter_default()
  {
  	$this->toolkit->setConf('profile', array());

  	$profiler = $this->toolkit->getProfileReporter();
  	$this->assertIsA($profiler, 'lmbProfilePanelReporter');
  }

  function testGetProfileReporter_customProfiler()
  {
    $this->toolkit->setConf('profile', array('profile_reporter' => 'lmbProfileTableReporter'));

    $profiler = $this->toolkit->getProfileReporter();
    $this->assertIsA($profiler, 'lmbProfileTableReporter');
  }
}


