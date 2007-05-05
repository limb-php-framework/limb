/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: flash.js 5810 2007-05-05 09:22:35Z pachanga $
 * @package    js
 */

Limb.namespace('Limb.Flash');

Limb.Flash.detectVersion = function(requiredVersion)
{
  var flashVersion = 0;

  if(!navigator.plugins)
    return false;

  if(Limb.Browser.is_win_ie)
  {
    var flashPresent = false;

    for(var version = requiredVersion; version<10; version++)
    {
      try
      {
        flashPresent = flashPresent || new ActiveXObject('ShockwaveFlash.ShockwaveFlash.' + version);
      }
      catch(e) {}
    }
    return flashPresent;
  }

  if(navigator.plugins["Shockwave Flash 2.0"]
      || navigator.plugins["Shockwave Flash"])
  {
    var isVersion2 = navigator.plugins["Shockwave Flash 2.0"] ? " 2.0" : "";
    var flashDescription = navigator.plugins["Shockwave Flash" + isVersion2].description;

    var flashVersion = parseInt(flashDescription.substring(16));
  }

  if(navigator.userAgent.indexOf("WebTV") != -1) actualVersion = 4;

  if(flashVersion < requiredVersion)
    return false;

  return true;
}

