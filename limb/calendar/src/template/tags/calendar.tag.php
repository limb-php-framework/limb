<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: calendar.tag.php 5536 2007-04-05 11:42:44Z pachanga $
 * @package    calendar
 */
require_once('limb/wact/src/tags/form/input.tag.php');

/**
* @tag limb:CALENDAR
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
    $this->setAttribute('type', 'hidden');

    $this->_ensureIdAttribute();

    parent :: prepare();
  }

  protected function _ensureIdAttribute()
  {
    if(!$this->hasAttribute('id'))
      $this->setAttribute('id', $this->getServerId());
  }

  function preGenerate($code)
  {
    parent :: preGenerate($code);

    $this->_createIframe($code);
  }

  function generateContents($code)
  {
    $this->_createUIField($code);
    $this->_createButton($code);
    $this->_removeJunkyAttributes();

    parent :: generateContents($code);
  }

  protected function _createIframe($code)
  {
    $date_iframe_id = $this->_getFrameId();
    $id = $this->getServerId();

    $code->writeHtml('<iframe tabindex="-1000" width=168 height=190 name="' . $date_iframe_id . '" id="' . $date_iframe_id . '"
          src="' . LIMB_HTTP_SHARED_PATH . 'calendar/ipopeng.htm" scrolling="no"
          frameborder="0" style="border:2px ridge; visibility:visible; z-index:999; position:absolute; left:-500px; top:0px;"' .
          ' onload="init_calendar_' . $id . '()"></iframe>');
  }

  protected function _getFrameId()
  {
    //default-month:theme-name[:agenda-file[:context-name[:plugin-file]]]

    $params = array();

    if($default_month = $this->getAttribute('default_month'))
    {
      $params[0] = $default_month;
      $this->removeAttribute('default_month');
    }
    else
      $params[0] = 'gToday';

    if($this->getAttribute('theme'))
    {
      $params[1] = $this->getAttribute('theme');
      $this->removeAttribute('theme');
    }
    else
      $params[1] = lmbToolkit :: instance()->getLocale();

    if($this->getAttribute('agenda'))
    {
      $params[2] = $this->getAttribute('agenda');
      $this->removeAttribute('agenda');
    }
    else
      $params[2] = '';

    if($this->getAttribute('context_name'))
    {
      $params[3] = $this->getAttribute('context_name');
      $this->removeAttribute('context_name');
    }
    else
      $params[3] = 'gfPop';

    if($this->getAttribute('plugin'))
    {
      $params[4] = $this->getAttribute('plugin');
      $this->removeAttribute('plugin');
    }

    return implode(':', $params);
  }

  protected function _getCalendarId()
  {
    if($id = $this->getAttribute('calendar_id'))
      return $id;

    return '_' . $this->getServerId();
  }

  protected function _createButton($code)
  {
    $parent = $this->findParentByClass('WactFormTag');
    $id = $this->getServerId();
    $form_id = $parent->getServerId();
    $calendar_id = $this->_getCalendarId();

    if(!$function = $this->getAttribute('function'))
      $function = "fPopCalendar(document.getElementById(\"{$calendar_id}\"), document.getElementById(\"{$id}\"))";
    else
      $this->removeAttribute('function');

    if(!$button = $this->getAttribute('button'))
      $button = LIMB_HTTP_SHARED_PATH . "calendar/calbtn.gif";
    else
      $this->removeAttribute('button');

    $code->writeHtml("<a href='javascript:void(0)' onclick='gfPop.". $function .";return false;' tabindex='-1000'>" .
                     "<img name='popcal' align='absbottom' src='". $button ."' width='34' height='22' border='0' alt=''></a>");
  }

  function _createUIField($code)
  {
    $id = $this->getServerId();
    $calendar_id = $this->_getCalendarId();
    $attributes = '';
    foreach($this->getAttributesAsArray() as $key => $value)
    {
      if($key == 'id' || $key == 'name' || $key == 'type')
        continue;
      $attributes .= "$key='$value' ";
    }
    $code->writeHtml("<input type='text' id='$calendar_id' $attributes />");
    $code->writeHtml("<script>
                     function init_calendar_$id()
                     {
                       var hidden=document.getElementById('$id');
                       var input=document.getElementById('$calendar_id');
                       input.value=gfPop.fStampToString(hidden.value * 1);

                       var prev = function(){};
                       if(hidden.form.onsubmit)
                         prev = hidden.form.onsubmit;

                       hidden.form.onsubmit = function() {
                        hidden.value=gfPop.fStringToStamp(input.value);
                        prev();
                       };
                     }
                     </script>");
  }

  function _removeJunkyAttributes()
  {
    foreach($this->getAttributesAsArray() as $key => $value)
    {
      if($key == 'id' || $key == 'name' || $key == 'type')
        continue;
      $this->removeAttribute($key);
    }
  }
}

?>