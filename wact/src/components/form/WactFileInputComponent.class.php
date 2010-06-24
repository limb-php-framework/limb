<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * Represents an HTML input type="file" tag
 *
 * (Someday someone is actually going to need to upload something.
 * Maybe then they will come write some nice methods for this
 * tag).
 * @package wact
 * @version $Id: WactFileInputComponent.class.php 7686 2009-03-04 19:57:12Z korchasa $
 */
class WactFileInputComponent extends WactInputComponent
{
  /**
  * We can't get a meaningful 'value' attribute for file upload controls
  * after form submission - the value would need to be the full path to the
  * file on the client machine and we don't have a handle on that
  * information. The component's 'value' is instead set to the relevant
  * portion of the $_FILES array, allowing initial validation of uploaded
  * files w/ WACT.
  */
  function getValue()
  {
  }
}


