/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: events.js 5436 2007-03-30 07:30:57Z tony $
 * @package    js
 */

Limb.namespace('Limb.events');

Limb.events.add_event = function (control, type, fn, use_capture)
{
 if (control.addEventListener)
 {
   control.addEventListener(type, fn, use_capture);
   return true;
 }
 else if (control.attachEvent)
 {
   var r = control.attachEvent("on" + type, fn);
   return r;
  }
}
