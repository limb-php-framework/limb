<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
require_once('limb/wact/src/tags/form/input.tag.php');
require_once('limb/calendar/src/lmbCalendarWidget.class.php');

/**
 * @tag datetime
 * @forbid_end_tag
 * @package calendar
 * @version $Id: datetime.tag.php 7486 2009-01-26 19:13:20Z pachanga $
 */
class lmbDatetimeTag extends WactInputTag
{
  function getRenderedTag()
  {
    return 'input';
  }

  function prepare()
  {
    $this->setAttribute('type', 'text');
    parent :: prepare();
  }

  function generateAfterCloseTag($code)
  {
    parent :: generateAfterCloseTag($code);

    if(!$lang = $this->getAttribute('lang'))
      $lang = 'en';

    if(!$this->hasAttribute('stripped'))
      $stripped = true;
    else
      $stripped = $this->getBoolAttribute('stripped');

    $widget = new lmbCalendarWidget($lang, $stripped);

    if($format = $this->getAttribute('format'))
    {
      $widget->setOption('ifFormat', $format);
      $widget->setOption('daFormat', $format);
    }
    else
    {
      $widget->setOption('ifFormat', '%Y-%m-%d');
      $widget->setOption('daFormat', '%Y-%m-%d');
    }

    $code->writeHTML($widget->loadFiles() .
                     $widget->makeButton($this->getAttribute('id'), 
                                         array(),
                                         array('src' => $this->getAttribute('src'))));

  }

}


