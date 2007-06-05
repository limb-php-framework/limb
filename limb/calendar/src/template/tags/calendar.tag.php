<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
require_once('limb/wact/src/tags/form/input.tag.php');
require_once('limb/calendar/src/lmbCalendarWidget.class.php');

/**
* @tag datetime,limb:CALENDAR
* @forbid_end_tag
*/
class lmbCalendarTag extends WactInputTag
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

    $widget = new lmbCalendarWidget($lang);

    if($format = $this->getAttribute('format'))
    {
      $widget->setOption('ifFormat', $format);
      $widget->setOption('daFormat', $format);
    }
    else
    {
      $widget->setOption('ifFormat', '%Y-%m-%d %H:%M');
      $widget->setOption('daFormat', '%Y-%m-%d %H:%M');
    }

    $code->writeHTML($widget->loadFiles() .
                     $widget->makeButton($this->getAttribute('id')));
  }

}

?>